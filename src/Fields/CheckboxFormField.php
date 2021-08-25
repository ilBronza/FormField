<?php

namespace IlBronza\FormField\Fields;

class CheckboxFormField extends RadioFormField
{
	public $multiple = true;

	public $htmlClasses = [
			'uk-checkbox'
		];

	public function transformValueBeforeStore($parameters)
	{
		if(is_array($parameters))
			return implode(",", $parameters);

		die('gestire campi diversi da array');
	}
}

