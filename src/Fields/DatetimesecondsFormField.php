<?php

namespace IlBronza\FormField\Fields;

class DatetimesecondsFormField extends DatetimeFormField
{
	public $format = 'Y-m-d\TH:i:s';
	public $step = 'any';
}