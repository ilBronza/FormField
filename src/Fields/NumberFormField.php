<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class NumberFormField extends FormField implements FormFieldInterface
{
	public ? int $decimals = null;

	use SingleValueFormFieldTrait;

	public function isInteger() : bool
	{
		return $this->getStep() == 1;
	}

	public $htmlClasses = [
			'uk-input'
		];

	public function getDecimals() : ? int
	{
		if($this->decimals)
			return $this->decimals;

		if($this->rulesContain('numeric'))
			return null;

		elseif($this->rulesContain('integer'))
			return 0;
	}

	public function getStep()
	{
		if(isset($this->step))
			return $this->step;

		if($this->rulesContain('numeric'))
			return 'any';

		elseif($this->rulesContain('integer'))
			return 1;
	}
}