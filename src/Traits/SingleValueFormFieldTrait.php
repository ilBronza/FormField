<?php

namespace IlBronza\FormField\Traits;

use Illuminate\Database\Eloquent\Model;
use \IlBronza\Form\Form;

trait SingleValueFormFieldTrait
{
	static public function renderValueForView($value) : ? string
	{
		return $value;
	}

	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	public function getDefaultValue()
	{
		if(! $this->form)
			return null;

		if($databaseField = $this->form->getDatabaseField($this->name))
			return $databaseField->getDefaultValue();

		return $this->default;
	}

	public function getValue()
	{
		if(isset($this->value))
			return $this->value;

		if($this->model)
			return $this->getModelValueByName($this->model, $this->name);

		if($model = $this->getModel())
			return $this->getModelValueByName($model, $this->name);

		if(($this->form)&&($this->form->model))
		{
			if($this->form->model->exists)
				return $this->getModelValueByName($this->form->model, $this->name);

			if($value = $this->getModelValueByName($this->form->model, $this->name))
				return $value;

			if($default = $this->getDefaultValue())
				return $default;
		}

		if(! empty($this->default))
			return $this->default;

		return null;
		// throw new \Exception('Nessun model da dove prendere il dato');
	}

	public function parseValueBeforeRender($value)
	{
		if($value instanceof Model)
			return $value->getKey();

		return $value;
	}

	public function getNumberFormOldValue()
	{
		try
		{
			if($this->isInteger())
			{
				if(is_numeric($this->getFormOldValue()))
					return floor($this->getFormOldValue());

				return null;
			}
		}
		catch(\Throwable $e)
		{
			return $e->getMessage();
		}

		return $this->getFormOldValue();
	}

	public function getFormOldValue()
	{
		try
		{
			return old(
				$this->getFormOldName(),
				$this->parseValueBeforeRender($this->getValue())
			);			
		}
		catch(\Throwable $e)
		{
			return $e->getMessage();
		}
	}

	public function mustShowPlaceholder()
	{
		if(is_null($this->placeholder))
			if($this->form)
				return $this->form->mustShowPlaceholder;

		return !! $this->placeholder;
	}
}