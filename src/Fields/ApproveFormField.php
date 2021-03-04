<?php

namespace ilBronza\FormField\Fields;

use Carbon\Carbon;
use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\Fields\ListValueFormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\ListValueFormFieldTrait;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class ApproveFormField extends BooleanFormField implements FormFieldInterface, ListValueFormFieldInterface
{
	public function getPossibleValuesArray()
	{
		return $this->compulsoryValues;
	}

	public function transformValueBeforeStore($value)
	{
		if($value)
			return Carbon::now();

		return ;
	}
}

