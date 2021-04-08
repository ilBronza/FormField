<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class GoogleAddressFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $htmlClasses = [
			'uk-input'
		];
}