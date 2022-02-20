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

	public $nullableValues = ['true' => 'true', 'false' => 'false', 'null' => 'null'];
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
		$value = old(
					$this->getFormOldName(),
					$this->parseValueBeforeRender($this->getValue())
				);

		if(($value === true)||($value === 1)||($value === "1"))
			return $this->nullableValues['true'];

		if(($value === false)||($value === 0)||($value === "0"))
			return $this->nullableValues['false'];

		if($default = $this->getDefaultValue())
			return $default;

		if(isset($this->default))
		{
			if(($this->default === true)||($this->default === 1)||($this->default === "1"))
				return $this->nullableValues['true'];

			if(($this->default === false)||($this->default === 0)||($this->default === "0"))
				return $this->nullableValues['false'];
		}

		if($value === null)
			return $this->nullableValues['null'];

		throw new \Exception('problemi a gestire il booleano per ' . $value);
	}
}

