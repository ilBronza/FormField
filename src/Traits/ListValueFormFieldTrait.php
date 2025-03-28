<?php

namespace IlBronza\FormField\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use \IlBronza\Form\Form;

use function dd;

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
		// dd('qua impostare una cache per public function getPossibleEnumValues()');
        if($this->hasRuleInArray())
            return $this->getPossibleValuesFromRules();

        // $databaseField = $this->form->allDatabaseFields[$this->name];
        //$databaseField->type

        try
        {
	        // dd('qua impostare una cache per public function getPossibleEnumValues()');
            $expression = \DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"');
            $expression = $expression->getValue(\DB::connection()->getQueryGrammar());
            $_enumStr = \DB::select($expression);
        }
        catch(\Exception $e)
        {
	        // dd('qua impostare una cache per public function getPossibleEnumValues()');
            $expression = \DB::raw('SHOW COLUMNS FROM ' . $this->getModel()->getTable() . ' WHERE Field = "' . $this->name . '"');
            // $expression = $expression->getValue(\DB::connection()->getQueryGrammar());
            $_enumStr = \DB::select($expression);            
        }

        if(! isset($_enumStr[0]))
            return [];

        $enumStr = $_enumStr[0]->Type;

        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        return $matches[1] ?? [];
    }

    public function getShowValue()
    {
        $model = $this->getModel();

        if(! $relation = $this->getRelationshipName())
        {
			if($this->list ?? null)
			{
				$key = $model->{$this->name};

				if(! $key)
					if(! is_null($this->default))
						$key = $this->default;

				if(isset($this->list[$key]))
					return $this->list[$key];
			}

	        return $model->{$this->name};
        }

        try
        {
            $getterMethod = 'get' . ucfirst($relation);
            $relatedModels = $model->{$getterMethod}();            
        }
        catch(\Exception $e)
        {
            $relatedModels = $model->$relation()->get();
        }

        if(! $relatedModels)
            return null;

        if(is_string($relatedModels))
                    return view('formfield::uikit.show.__links', ['links' => [
                        [
                            'name' => $relatedModels,
                            'link' => ''
                        ]
                    ]])->render();

        if($relatedModels instanceof Model)
            $relatedModels = collect([$relatedModels]);

        if(! $relatedModels instanceof Collection)
            throw new \Exception('Il metodo ' . $getterMethod . ' deve restituire un modello o una collection');

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