<?php

namespace IlBronza\FormField\Traits;

use IlBronza\Form\FormFieldset;
use Illuminate\Database\Eloquent\Model;
use \IlBronza\Form\Form;

trait FormFieldSetter
{
	/**
	 * DEPRECATA IN FAVORE Di setModel DOGODO SISTODO TODO
	 **/
	public function assignModel(Model $model)
	{
		return $this->setModel($model);
	}

	public function setModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}

	public function setForm(Form $form)
	{
		$this->form = $form;
	}

	public function setFieldset(FormFieldset $fieldset)
	{
		$this->fieldset = $fieldset;
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