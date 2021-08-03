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

	public $format = 'Y-m-d';

	public function getStep()
	{
		return $this->step;
	}

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

		if((isset($value))&&(class_basename($value) == 'Carbon'))
			return $value->format($this->format);

		return $value ?? null;
		// throw new \Exception('Nessun model da dove prendere il dato');
	}

}