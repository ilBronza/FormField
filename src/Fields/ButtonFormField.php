<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;

class ButtonFormField extends FormField implements FormFieldInterface
{
	public $type = 'submit';

	public $htmlClasses = [
			'uk-button'
		];

	static public function renderValueForView($value) : ? string
	{
		return $value;
	}

	public function getValue()
	{
		if($this->value)
			return $this->value;

		return 1;
	}

	public function getInputSizeClass() : string
	{
		return str_replace("form", "button", $this->inputSizeClass);
	}

}