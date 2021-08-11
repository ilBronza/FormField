<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class FileFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
		];

	public function getMethod()
	{
		if($this->form->method)
			return $this->form->method;

		throw new \Exception('Gestire method di diversa provenienza se in mancanza di form');
	}

	public function getUploadingUrl()
	{
		if($this->form->action)
			return $this->form->action;

		throw new \Exception('Gestire url di diversa provenienza se in mancanza di form');
	}
}