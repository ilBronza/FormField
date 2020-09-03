<?php

namespace ilBronza\FormField\Traits;

use Illuminate\Support\Str;

trait FormFieldGetter
{
	/**
	 * get model value
	 *
	 * if model has own field-specific getter function, call it and return value
	 * if model has own general getter function, call it and return value
	 * if model has field value not empty return value
	 *
	 * @param Model $model, string fieldName
	 * @return mixed
	 **/
	public function getModelValueByName($model, string $fieldName)
	{
		$getterMethod = 'get' . ucfirst(Str::camel($fieldName));

		if(method_exists($model, $getterMethod))
			return $model->$getterMethod();

		if(method_exists($model, 'getFromFieldValue'))
			return $model->getFromFieldValue($fieldName);

		if(! empty($value = $model->{$fieldName}))
			return $value;

		return $model->{$fieldName};

		throw new \Exception('Il model ' . class_basename($model) . ' non ha il metodo getter per ' . $fieldName . ' o il campo non esiste a db');
	}

	public function getModel()
	{
		if($this->model)
			return $this->model;

		return $this->form->model;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getRenderType()
	{
		if(empty($this->renderAs))
			return $this->type;
	}

	public function getDataAttributes()
	{
		return $this->data;
	}

	public function getContainerId()
	{
		return $this->containerId ?? $this->id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getFormOldName()
	{
		return $this->name;
	}

	public function getHtmlClasses()
	{
		return $this->htmlClasses;
	}

	public function getHtmlRowClassesString()
	{
		return "";
	}

	public function getHtmlClassesString()
	{
		return implode(" ", $this->getHtmlClasses());
	}
}