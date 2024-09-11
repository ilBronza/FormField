<?php

namespace IlBronza\FormField\Traits;

use IlBronza\FormField\FormField;
use IlBronza\Form\FormFieldset;
use Illuminate\Database\Eloquent\Model;
use \IlBronza\Form\Form;

use function in_array;

trait FormFieldSetter
{
	public function addProblem(string $problem) : self
	{
		if(! in_array($problem, $this->problems))
			$this->problems[] = $problem;

		return $this;
	}

	public function setDblClickCopy(bool $value) : self
	{
		$this->dblClickCopy = $value;

		return $this;
	}

	public function setLastOfType(bool $value = true)
	{
		$this->lastOfType = $value;
	}
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

	public function setParent(FormField $field)
	{
		$this->parent = $field;
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