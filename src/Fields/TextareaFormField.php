<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class TextareaFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
			'uk-textarea'
		];
}