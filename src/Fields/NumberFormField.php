<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class NumberFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public function getStep()
	{
		if($this->rulesContain('numeric'))
			return 'any';

		elseif($this->rulesContain('integer'))
			return 1;
	}
}