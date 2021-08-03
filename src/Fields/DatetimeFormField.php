<?php

namespace IlBronza\FormField\Fields;

class DatetimeFormField extends DateFormField
{
	public $step = 60;
	public $format = 'Y-m-d\TH:i';

}