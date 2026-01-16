<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class TextareaFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public ? string $inlineStyle = 'height: 90px;';

	public $htmlClasses = [
			'uk-textarea'
		];
}