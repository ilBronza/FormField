<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;

trait SingleValueFormFieldTrait
{
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	public function getValue()
	{
		if(isset($this->value))
			return $this->value;

		if($this->model)
			return $this->getModelValueByName($this->model, $this->name);

		if(($this->form)&&($this->form->model))
			return $this->getModelValueByName($this->form->model, $this->name);

		if(! empty($this->default))
			return $this->default;

		return null;
		// throw new \Exception('Nessun model da dove prendere il dato');
	}

	public function getFormOldValue()
	{
		return old(
			$this->getFormOldName(),
			$this->getValue()
		);
	}

	public function mustShowPlaceholder()
	{
		if(is_null($this->placeholder))
			if($this->form)
				return $this->form->mustShowPlaceholder;

		return !! $this->placeholder;
	}
}