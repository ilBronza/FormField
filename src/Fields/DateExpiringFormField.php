<?php

namespace IlBronza\FormField\Fields;

use Carbon\Carbon;

use function array_push;

class DateExpiringFormField extends DateFormField
{
	public $expiringDate = null;
	public bool $considerNullAsValid = true;

	public function getExpiringDate()
	{
		return $this->expiringDate;
	}

	public function nullIsValid() : bool
	{
		return $this->considerNullAsValid;
	}

	public function isExpired()
	{
		if(is_null($value = $this->getValue()))
			if($this->nullIsValid())
				return false;

		if($value >= Carbon::now())
			return false;

		$this->addProblem(trans('formfields::problems.dateExpired'));

		return true;
	}

	public function getHtmlClasses()
	{
		$classes = parent::getHtmlClasses();

		if($this->isExpired())
			$classes[] = 'uk-form-danger';
		else
			$classes[] = 'uk-form-success';

		return $classes;
	}
}