<?php

namespace IlBronza\FormField\Fields;

use Carbon\Carbon;
use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\Fields\ListValueFormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\ListValueFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

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

