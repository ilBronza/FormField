<?php

namespace IlBronza\FormField\Traits;

trait FormFieldOpener
{
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