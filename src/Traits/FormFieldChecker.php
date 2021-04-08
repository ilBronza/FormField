<?php

namespace IlBronza\FormField\Traits;

trait FormFieldChecker
{
	public function isClosed()
	{
		return $this->closed;
	}

	public function isReadOnly()
	{
		return $this->readOnly;
	}

	public function isDisabled()
	{
		return $this->disabled;
	}

	public function isMultiple()
	{
		return $this->multiple;
	}

	public function isNullable()
	{
		return ! $this->isRequired();
	}

	public function isRequired()
	{
		return $this->required;
	}

	public function rulesContain(string $rule)
	{
		return in_array($rule, array_keys($this->rules));
	}

	public function hasAutocomplete()
	{
		return false;		
	}

	public function mustShowPlaceholder()
	{
		if((is_null($this->placeholder))&&(isset($this->form)))
			return $this->form->mustShowPlaceholder;

		return !! $this->placeholder;
	}
}