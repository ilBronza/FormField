<?php

namespace IlBronza\FormField\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait FormFieldGetter
{
	public function getFetcherData()
	{
		return $this->fetcher;
	}

	public function getEditorAction()
	{
		return $this->editorAction ?? false;
	}

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

		if($model->{$fieldName} !== null)
			return $model->{$fieldName};

		return $this->getDefaultValue();

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
		if($this->containerId)
			return $this->containerId;

		$pieces = [];

		if($this->form ?? false)
			$pieces[] = $this->form->getId();

		$pieces[] = $this->getId();
		$pieces[] = 'container';

		return implode("-", $pieces);
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
		return str_replace("]", "", str_replace("[", ".", $this->name));
	}

	public function getHtmlClasses()
	{
		return $this->htmlClasses;
	}

	public function addRowHtmlClass(string $class)
	{
		$this->rowHtmlClasses[] = $class;
	}

	public function getHtmlRowClassesString() : string
	{
		return " " . implode(" ", $this->rowHtmlClasses) . " ";
	}

	public function getFetcherFieldClasses()
	{
		if(! isset($this->fetcher))
			return null;

		return $this->fetcher['type'] ?? 'ajaxfetcher';
	}

	public function getSuffix()
	{
		return $this->suffix ?? false;
	}

	public function getPrefix()
	{
		return $this->prefix ?? false;
	}
}