<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;

trait ListValueFormFieldTrait
{
    public function getPossibleEnumValuesArray()
    {
        $values = $this->getPossibleEnumValues();

        $result = [];

        foreach($values as $value)
            $result[$value] = $value;

        return $result;
    }

	public function getPossibleEnumValues()
	{
        $_enumStr = \DB::select(\DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"'));

        $enumStr = $_enumStr[0]->Type;
        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        return $matches[1] ?? [];
	}
}