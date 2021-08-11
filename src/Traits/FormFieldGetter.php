<?php

namespace IlBronza\FormField\Traits;

use Illuminate\Database\Eloquent\Model;
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
		{
			if ($value instanceof Model)
				return $value->getKey();

			return $value;
		}

		return $model->{$fieldName};

		throw new \Exception('Il model ' . class_basename($model) . ' non ha il metodo getter per ' . $fieldName . ' o il campo non esiste a db');
	}

	public function assignModel(Model $model)
	{
		$this->model = $model;

		return $this;
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
		if($this->id === false)
			return false;

		$this->id = $this->id ?? Str::slug($this->name);

		if(preg_match('/^\d/', $this->id) === 1)
			$this->id = 'id-' . $this->id;

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

	public function getFetcherFieldClasses()
	{
		if(! isset($this->fetcher))
			return null;

		return $this->fetcher['type'] ?? 'ajaxfetcher';
	}
}