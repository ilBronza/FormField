<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

class MoneyFormField extends NumberFormField
{
	public $icon = 'euro-sign';
	public $viewName = 'text';


	public $htmlClasses = [
		'dtprice',
		'uk-input'
	];

	public function getStep()
	{
		return 0.01;
	}
}