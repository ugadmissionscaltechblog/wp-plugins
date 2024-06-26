<?php

namespace Sabre\VObject\Property\ICalendar;

use
    Sabre\VObject\Property,
    Sabre\VObject\Parser\MimeDir,
    Sabre\VObject\DateTimeParser;

/**
 * Period property
 *
 * This object represents PERIOD values, as defined here:
 *
 * http://tools.ietf.org/html/rfc5545#section-3.8.2.6
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Period extends Property {

    /**
     * In case this is a multi-value property. This string will be used as a
     * delimiter.
     *
     * @var string|null
     */
    public $delimiter = ',';

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

        $this->setValue(explode($this->delimiter, $val));

    }

    /**
     * Returns a raw mime-dir representation of the value.
     *
     * @return string
     */
    public function getRawMimeDirValue() {

        return implode($this->delimiter, $this->getParts());

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

        return "PERIOD";

    }

    /**
     * Sets the json value, as it would appear in a jCard or jCal object.
     *
     * The value must always be an array.
     *
     * @param array $value
     * @return void
     */
    public function setJsonValue(array $value) {

        $value = array_map(
            function($item) {

                return strtr(implode('/', $item), array(':' => '', '-' => ''));

            },
            $value
        );
        parent::setJsonValue($value);

    }

    /**
     * Returns the value, in the format it should be encoded for json.
     *
     * This method must always return an array.
     *
     * @return array
     */
    public function getJsonValue() {

        $return = array();
        foreach($this->getParts() as $item) {

            list($start, $end) = explode('/', $item, 2);

            $start = DateTimeParser::parseDateTime($start);

            // This is a duration value.
            if ($end[0]==='P') {
                $return[] = array(
                    $start->format('Y-m-d\\TH:i:s'),
                    $end
                );
            } else {
                $end = DateTimeParser::parseDateTime($end);
                $return[] = array(
                    $start->format('Y-m-d\\TH:i:s'),
                    $end->format('Y-m-d\\TH:i:s'),
                );
            }

        }

        return $return;

    }

}
