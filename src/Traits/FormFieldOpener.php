<?php

namespace IlBronza\FormField\Traits;

trait FormFieldOpener
{
	public function hasConditionalOpener() : bool
	{
		return $this->hasOpener() && array_key_exists('value', $this->opener);
	}

	public function getOpenerTargetNames() : array
	{
		return $this->opener['targetNames'] ?? [$this->getOpenerTargetName()];
	}

	public function getOpenerValue()
	{
		return $this->opener['value'];
	}

	public function hasOpener()
	{
		return !! ($this->opener ?? false);
	}

	public function getOpenerTargetName()
	{
		if($this->opener['targetName'] ?? false)
			return $this->opener['targetName'];

		return ;
	}

	public function getOpenerRequiredAttribute() : bool
	{
		return !! ($this->opener['required'] ?? false);
	}
}
