<?php

namespace Sabre\VObject\Property;

use
    Sabre\VObject\Property,
    Sabre\VObject\Component,
    Sabre\VObject\Parser\MimeDir,
    Sabre\VObject\Document;

/**
 * Text property
 *
 * This object represents TEXT values.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Text extends Property {

    /**
     * In case this is a multi-value property. This string will be used as a
     * delimiter.
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * List of properties that are considered 'structured'.
     *
     * @var array
     */
    protected $structuredValues = array(
        // vCard
        'N',
        'ADR',
        'ORG',
        'GENDER',

        // iCalendar
        'REQUEST-STATUS',
    );

    /**
     * Some text components have a minimum number of components.
     *
     * N must for instance be represented as 5 components, separated by ;, even
     * if the last few components are unused.
     *
     * @var array
     */
    protected $minimumPropertyValues = array(
        'N' => 5,
        'ADR' => 7,
    );

    /**
     * Creates the property.
     *
     * You can specify the parameters either in key=>value syntax, in which case
     * parameters will automatically be created, or you can just pass a list of
     * Parameter objects.
     *
     * @param Component $root The root document
     * @param string $name
     * @param string|array|null $value
     * @param array $parameters List of parameters
     * @param string $group The vcard property group
     * @return void
     */
    public function __construct(Component $root, $name, $value = null, array $parameters = array(), $group = null) {

        // There's two types of multi-valued text properties:
        // 1. multivalue properties.
        // 2. structured value properties
        //
        // The former is always separated by a comma, the latter by semi-colon.
        if (in_array($name, $this->structuredValues)) {
            $this->delimiter = ';';
        }

        parent::__construct($root, $name, $value, $parameters, $group);

    }


    /**
     * Sets a raw value coming from a mimedir (iCalendar/vCard) file.
     *
     * This has been 'unfolded', so only 1 line will be passed. Unescaping is
     * not yet done, but parameters are not included.
     *
     * @param string $val
     * @return void
     */
    public function setRawMimeDirValue($val) {

        $this->setValue(MimeDir::unescapeValue($val, $this->delimiter));

    }

    /**
     * Sets the value as a quoted-printable encoded string.
     *
     * @param string $val
     * @return void
     */
    public function setQuotedPrintableValue($val) {

        $val = quoted_printable_decode($val);

        // Quoted printable only appears in vCard 2.1, and the only character
        // that may be escaped there is ;. So we are simply splitting on just
        // that.
        //
        // We also don't have to unescape \\, so all we need to look for is a ;
        // that's not preceeded with a \.
        $regex = '# (?<!\\\\) ; #x';
        $matches = preg_split($regex, $val);
        $this->setValue($matches);

    }

    /**
     * Returns a raw mime-dir representation of the value.
     *
     * @return string
     */
    public function getRawMimeDirValue() {

        $val = $this->getParts();

        if (isset($this->minimumPropertyValues[$this->name])) {
            $val = array_pad($val, $this->minimumPropertyValues[$this->name], '');
        }

        foreach($val as &$item) {

            if (!is_array($item)) {
                $item = array($item);
            }

            foreach($item as &$subItem) {
                $subItem = strtr(
                    $subItem,
                    array(
                        '\\' => '\\\\',
                        ';'  => '\;',
                        ','  => '\,',
                        "\n" => '\n',
                        "\r" => "",
                    )
                );
            }
            $item = implode(',', $item);

        }

        return implode($this->delimiter, $val);

    }

    /**
     * Returns the value, in the format it should be encoded for json.
     *
     * This method must always return an array.
     *
     * @return array
     */
    public function getJsonValue() {

        // Structured text values should always be returned as a single
        // array-item. Multi-value text should be returned as multiple items in
        // the top-array.
        if (in_array($this->name, $this->structuredValues)) {
            return array($this->getParts());
        } else {
            return $this->getParts();
        }

    }

    /**
     * Returns the type of value.
     *
     * This corresponds to the VALUE= parameter. Every property also has a
     * 'default' valueType.
     *
     * @return string
     */
    public function getValueType() {

        return "TEXT";

    }

    /**
     * Turns the object back into a serialized blob.
     *
     * @return string
     */
    public function serialize() {

        // We need to kick in a special type of encoding, if it's a 2.1 vcard.
        if ($this->root->getDocumentType() !== Document::VCARD21) {
            return parent::serialize();
        }

        $val = $this->getParts();

        if (isset($this->minimumPropertyValues[$this->name])) {
            $val = array_pad($val, $this->minimumPropertyValues[$this->name], '');
        }

        // Imploding multiple parts into a single value, and splitting the
        // values with ;.
        if (count($val)>1) {
            foreach($val as $k=>$v) {
                $val[$k] = str_replace(';','\;', $v);
            }
            $val = implode(';', $val);
        } else {
            $val = $val[0];
        }

        $str = $this->name;
        if ($this->group) $str = $this->group . '.' . $this->name;
        foreach($this->parameters as $param) {

            if ($param->getValue() === 'QUOTED-PRINTABLE') {
                continue;
            }
            $str.=';' . $param->serialize();

        }



        // If the resulting value contains a \n, we must encode it as
        // quoted-printable.
        if (strpos($val,"\n") !== false) {

            $str.=';ENCODING=QUOTED-PRINTABLE:';
            $lastLine=$str;
            $out = null;

            // The PHP built-in quoted-printable-encode does not correctly
            // encode newlines for us. Specifically, the \r\n sequence must in
            // vcards be encoded as =0D=OA and we must insert soft-newlines
            // every 75 bytes.
            for($ii=0;$ii<strlen($val);$ii++) {
                $ord = ord($val[$ii]);
                // These characters are encoded as themselves.
                if ($ord >= 32 && $ord <=126) {
                    $lastLine.=$val[$ii];
                } else {
                    $lastLine.='=' . strtoupper(bin2hex($val[$ii]));
                }
                if (strlen($lastLine)>=75) {
                    // Soft line break
                    $out.=$lastLine. "=\r\n ";
                    $lastLine = null;
                }

            }
            if (!is_null($lastLine)) $out.= $lastLine . "\r\n";
            return $out;

        } else {
            $str.=':' . $val;
            $out = '';
            while(strlen($str)>0) {
                if (strlen($str)>75) {
                    $out.= mb_strcut($str,0,75,'utf-8') . "\r\n";
                    $str = ' ' . mb_strcut($str,75,strlen($str),'utf-8');
                } else {
                    $out.=$str . "\r\n";
                    $str='';
                    break;
                }
            }

            return $out;


        }

    }

    /**
     * Validates the node for correctness.
     *
     * The following options are supported:
     *   - Node::REPAIR - If something is broken, and automatic repair may
     *                    be attempted.
     *
     * An array is returned with warnings.
     *
     * Every item in the array has the following properties:
     *    * level - (number between 1 and 3 with severity information)
     *    * message - (human readable message)
     *    * node - (reference to the offending node)
     *
     * @param int $options
     * @return array
     */
    public function validate($options = 0) {

        $warnings = parent::validate($options);

        if (isset($this->minimumPropertyValues[$this->name])) {

            $minimum = $this->minimumPropertyValues[$this->name];
            $parts = $this->getParts();
            if (count($parts) < $minimum) {
                $warnings[] = array(
                    'level' => 1,
                    'message' => 'This property must have at least ' . $minimum . ' components. It only has ' . count($parts),
                    'node' => $this,
                );
                if ($options & self::REPAIR) {
                    $parts = array_pad($parts, $minimum, '');
                    $this->setParts($parts);
                }
            }

        }
        return $warnings;

    }
}
