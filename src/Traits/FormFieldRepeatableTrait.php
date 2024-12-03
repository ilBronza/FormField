<?php

namespace IlBronza\FormField\Traits;

use IlBronza\Buttons\Button;

use function is_null;

trait FormFieldRepeatableTrait
{
	public function isRepeatable() : bool
	{
		return ! ! $this->repeatable;
	}
}