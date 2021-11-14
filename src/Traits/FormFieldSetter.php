<?php

namespace IlBronza\FormField\Traits;

use \IlBronza\Form\Form;

trait FormFieldSetter
{
	public function assignModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}

	public function setForm(Form $form)
	{
		$this->form = $form;
	}

	public function setFetcher(array $options)
	{
		$this->fetcher = [];

		foreach($options as $key => $value)
			$this->fetcher['fetcher-' . $key] = $value;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

}