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
            throw new \Exception('Dichiara i possibili valori sulla rule:in per ' . $this->name);

        $pieces = explode(",", $rules['in']);

        return array_combine($pieces, $pieces);
    }

    public function hasRuleInArray() : bool
    {
        $rules = $this->rules;

        return !! isset($rules['in']);
    }

    public function getPossibleEnumValues()
    {
        if($this->hasRuleInArray())
            return $this->getPossibleValuesFromRules();

        // $databaseField = $this->form->allDatabaseFields[$this->name];
        //$databaseField->type

        try
        {
            $expression = \DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"');
            $expression = $expression->getValue(\DB::connection()->getQueryGrammar());
            $_enumStr = \DB::select($expression);
        }
        catch(\Exception $e)
        {
            $expression = \DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"');
            // $expression = $expression->getValue(\DB::connection()->getQueryGrammar());
            $_enumStr = \DB::select($expression);            
        }

        $enumStr = $_enumStr[0]->Type;

        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        return $matches[1] ?? [];
    }

    public function getShowValue()
    {
        $model = $this->getModel();

        if(! $relation = $this->getRelationshipName())
            return $model->{$this->name};

        $relatedModels = $model->$relation()->get();

        $links = $relatedModels->map(function($item)
            {
                try
                {
                    return [
                        'name' => $item->getName(),
                        'link' => $item->getShowUrl()
                    ];
                }
                catch(\Exception $e)
                {
                    return [
                        'name' => $item->getName() . ' | ' . $e->getMessage(),
                        'link' => ''
                    ];                    
                }
            });

        return view('formfield::uikit.show.__links', ['links' => $links])->render();

    }
}