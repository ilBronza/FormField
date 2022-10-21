<?php

namespace IlBronza\FormField\Fields;

class TimeFormField extends DateFormField
{
	public $step = 60;
	public $format = 'H:i';
	public $dateType = 'time';
	public $viewName = 'date';

}