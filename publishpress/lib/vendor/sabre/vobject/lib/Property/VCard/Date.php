<?php

namespace Sabre\VObject\Property\VCard;

use
    Sabre\VObject\DateTimeParser;

/**
 * Date property
 *
 * This object encodes vCard DATE values.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Date extends DateAndOrTime {

    /**
     * Returns the type of value.
     *
     * This corresponds to the VALUE= parameter. Every property also has a
     * 'default' valueType.
     *
     * @return string
     */
    public function getValueType() {

        return "DATE";

    }

    /**
     * Sets the property as a DateTime object.
     *
     * @param \DateTime $dt
     * @return void
     */
    public function setDateTime(\DateTime $dt) {

        $this->value = $dt->format('Ymd');

    }

}
