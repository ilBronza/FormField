<?php

namespace IlBronza\FormField\Fields;

use Carbon\Carbon;

use function array_push;

class DateExpiringFormField extends DateFormField
{
	public $expiringDate = null;

	public function getExpiringDate()
	{
		return $this->expiringDate;
	}

	public function isExpired()
	{
		if($this->getValue() >= Carbon::now())
			return false;

		$this->addProblem(trans('formfields::problems.dateExpired'));

		return true;
	}

	public function getHtmlClasses()
	{
		$classes = parent::getHtmlClasses();

		if($this->isExpired())
			$classes[] = 'uk-form-danger';

		return $classes;
	}
}