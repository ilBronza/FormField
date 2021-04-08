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

	public function getValue()
	{
		if(isset($this->value))
			$value = $this->value;

		else if($this->model)
			$value = $this->getModelValueByName($this->model, $this->name);

		else if(($this->form)&&($this->form->model))
			$value = $this->getModelValueByName($this->form->model, $this->name);

		else if(! empty($this->default))
			$value = $this->default;

		if(class_basename($value) == 'Carbon')
			return $value->format('Y-m-d\TH:i:s');

		return null;
		// throw new \Exception('Nessun model da dove prendere il dato');
	}

}