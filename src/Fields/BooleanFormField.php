<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\Fields\ListValueFormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\ListValueFormFieldTrait;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class BooleanFormField extends FormField implements FormFieldInterface, ListValueFormFieldInterface
{
	use SingleValueFormFieldTrait;
	use ListValueFormFieldTrait;

	public $nullableValues = ['true' => 1, 'false' => 0, 'null' => null];
	public $compulsoryValues = ['true' => 1, 'false' => 0];

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
}

