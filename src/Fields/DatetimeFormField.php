<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class DatetimeFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
			'uk-input',
			'jsdatetimepicker'
		];

	public function setStep(int $step)
	{
		$this->data['step'] = $step;

		return $this;
	}

	public function setFormat(string $format)
	{
		$this->data['format'] = $format;

		return $this;
	}
}