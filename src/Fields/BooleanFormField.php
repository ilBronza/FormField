<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\Fields\ListValueFormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\ListValueFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class BooleanFormField extends FormField implements FormFieldInterface, ListValueFormFieldInterface
{
	use SingleValueFormFieldTrait;
	use ListValueFormFieldTrait;

	public $nullableValues = ['true' => 'true', 'false' => 'false', 'null' => null];
	public $compulsoryValues = ['true' => 'true', 'false' => 'false'];

	public function getPossibleValuesArray()
	{
		if($this->isNullable())
			return $this->getNullableValues();

		return $this->getCompulsoryValues();
	}

	private function getNullableValues()
	{
		return $this->nullableValues;
	}

	private function getCompulsoryValues()
	{
		return $this->compulsoryValues;
	}

	public function getFormOldValue()
	{
		return (old(
					$this->getFormOldName(),
					$this->parseValueBeforeRender($this->getValue())
				))? 'true' : 'false';
	}
}

