<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;

trait FormFieldSetter
{
	public function setForm(Form $form)
	{
		$this->form = $form;
	}
}