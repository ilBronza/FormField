<?php

namespace ilBronza\FormField\Traits;

trait FormFieldDisplay
{
	public function getFieldTypeClass()
	{
		return $this->type;
	}


	public function getLabel()
	{
		if($this->label === true)
			return $this->getName();

		else if($this->label)
			return $this->label;

		if($this->form->mustShowLabel())
			return __('fields.' . $this->getName());

		return false;
	}

	public function getPlaceholder()
	{
		if($this->placeholder === true)
			return $this->getName();

		else if($this->placeholder)
			return $this->placeholder;

		return __('fields.' . $this->getName());
	}

	public function getTooltip()
	{
		if($this->tooltip === true)
			return $this->getName();

		else if($this->tooltip)
			return $this->tooltip;

		return false;
	}

	public function getReadOnlyText()
	{
		if($this->readOnlyText === true)
			return trans('formfield::formfield.readOnlyAlertMessage');

		else if($this->readOnlyText)
			return $this->readOnlyText;

		return false;
	}

}