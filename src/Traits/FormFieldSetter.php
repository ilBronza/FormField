<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;

trait FormFieldSetter
{
	public function setForm(Form $form)
	{
		$this->form = $form;
	}

	public function setFetcher(array $options)
	{
		$this->fetcher = [];

		foreach($options as $key => $value)
			$this->fetcher[$key] = $value;
	}
}