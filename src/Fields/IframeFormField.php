<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class IframeFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;
}