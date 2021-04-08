<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class NumberFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
			'uk-input'
		];

	public function getStep()
	{
		if($this->rulesContain('numeric'))
			return 'any';

		elseif($this->rulesContain('integer'))
			return 1;
	}
}