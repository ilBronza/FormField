<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class DateFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
			'uk-input'
		];

	public function parseValueBeforeRender($value)
	{
		if(class_basename($value) == 'Carbon')
			return $value->format('Y-m-d');

		return $value;
	}
}