<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;
use DB;

trait ListValueFormFieldTrait
{
    public function getPossibleEnumValues()
    {
        $values = $this->_getPossibleEnumValues();

        return array_combine ($values, $values);
    }

    public function isEnumOrSet()
    {
        $_enumStr = DB::select(\DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"'));

        if(strpos($_enumStr[0]->Type, 'enum') === 0)
            return true;

        if(strpos($_enumStr[0]->Type, 'set') === 0)
            return true;

        return false;
    }

    public function getPossibleEnumValuesArray()
    {
        if($this->isEnumOrSet())
            return $this->getPossibleEnumValues();

        $values = $this->getValue();

        return array_combine ($values, $values);
    }

	public function _getPossibleEnumValues()
	{
        $_enumStr = DB::select(\DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"'));

        $enumStr = $_enumStr[0]->Type;
        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        return $matches[1] ?? [];
	}
}