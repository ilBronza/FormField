<?php

namespace IlBronza\FormField\Traits;

use \IlBronza\Form\Form;

trait NEWMultipleValueFormFieldTrait
{
    /**
     * return the array of possible values filtered by selected keys
     * 
     * example: 
     * 
     * return [
     *     1 => 'value1',
     *     2 => 'value2',
     *     3 => 'value3'
     * ]
     * 
     * @return array
     **/
    public function getSelectedPossibleValuesArray() : array
    {
        $selected = $this->getValue();

        if(is_null($selected))
            return [];

        if(! is_array($selected))
            $selected = [$selected];

        $values = $this->getPossibleValuesArray();

        return array_intersect_key($values, array_flip($selected));
    }
}