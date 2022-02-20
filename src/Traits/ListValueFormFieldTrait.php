<?php

namespace IlBronza\FormField\Traits;

use \IlBronza\Form\Form;

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

    public function getPossibleValuesFromRules()
    {
        $rules = $this->rules;

        if(! isset($rules['in']))
            throw new \Exception('Dichiara i possibili valori sulla rule:in');

        $pieces = explode(",", $rules['in']);

        return array_combine($pieces, $pieces);
    }

    public function getPossibleEnumValues()
    {
        if(! isset($this->form->allDatabaseFields[$this->name]))
            return $this->getPossibleValuesFromRules();

        $databaseField = $this->form->allDatabaseFields[$this->name];
        //$databaseField->type

        // $_enumStr = \DB::select(\DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"'));

        // $enumStr = $_enumStr[0]->Type;

        $enumStr = $databaseField->type;
        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        return $matches[1] ?? [];
    }
}