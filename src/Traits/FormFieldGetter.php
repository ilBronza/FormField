<?php

namespace IlBronza\FormField\Traits;

use Exception;
use IlBronza\Form\Form;
use IlBronza\Form\FormFieldset;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait FormFieldGetter
{
	public function hasProblems() : bool
	{
		return !! $this->getProblems();
	}

	public function getProblems() : array
	{
		return $this->problems;
	}

	public function getProblemsString(string $separator = '<br />') : string
	{
		return implode($separator, $this->getProblems());
	}

	public function getProblemsCount() : int
	{
		return count($this->getProblems());
	}

	public function getDblClickCopy() : bool
	{
		return $this->dblClickCopy;
	}

	public function hasFieldsToFetch() : bool
	{
		return count($this->getFieldsToFetch()) > 0;
	}

	public function getFieldsToFetch() : array
	{
		return $this->fetchFieldValue;
	}

	public function getRepeatableFormfieldAjaxUrl() : string
	{
		if ($model = $this->getModel())
			return $model->getRepeatableFormfieldAjaxUrl();
	}

	public function getModel() : ?Model
	{
		if ($this->model)
			return $this->model;

		if ($model = $this->getParent()?->getModel())
			return $model;

		if ($fieldset = $this->getFieldset())
			if ($model = $fieldset->getModel())
				return $model;

		if ($form = $this->getForm())
			if ($model = $form->getModel())
				return $model;

		return null;
	}

	public function getParent() : ?FormField
	{
		return $this->parent;
	}

	public function getFieldset() : ?FormFieldset
	{
		return $this->fieldset;
	}

	public function getForm() : ?Form
	{
		if ($this->form)
			return $this->form;

		if ($fiedlset = $this->getFieldset())
			if ($form = $fiedlset->getForm())
				return $form;

		return null;
	}

	public function getRepeatableFieldKey() : ?string
	{
		if (($model = $this->getModel()) && ($model instanceof FormfieldModelCompatibilityInterface))
			return $model->getKey();

		return null;
	}

	public function getRules()
	{
		return $this->rules;
	}

	public function getRelationshipTypeLink()
	{
		$relationship = $this->getRelationshipName();

		$routeName = $relationship . ".index";

		if (Route::has($routeName))
			return route($routeName);

		$routeName = Str::plural($relationship) . ".index";

		if (Route::has($routeName))
			return route($routeName);

		$routeName = __('routenames.' . $routeName . 'Index');

		if (! Route::has($routeName))
			throw new Exception('Translate this missing route ' . $routeName . ' to link properly for ' . $relationship . ' index');

		return route($routeName);
	}

	public function getRelationshipName() : ?string
	{
		return $this->relation ?? null;
	}

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
	 * @param  Model  $model  , string fieldName
	 *
	 * @return mixed
	 **/
	public function getModelValueByName($model, string $fieldName)
	{
		$getterMethod = 'get' . ucfirst(Str::camel($fieldName));

		if (method_exists($model, $getterMethod))
			return $this->parseIfModels(
				$model->$getterMethod()
			);

		if (method_exists($model, 'getFromFieldValue'))
			return $model->getFromFieldValue($fieldName);

		if (! empty($value = $model->{$fieldName}))
		{
			if ($value instanceof Model)
				return $value->getKey();

			return $value;
		}

		if ($model->{$fieldName} !== null)
			return $model->{$fieldName};

		return $this->getDefaultValue();

		throw new Exception('Il model ' . class_basename($model) . ' non ha il metodo getter per ' . $fieldName . ' o il campo non esiste a db');
	}

	public function parseIfModels(mixed $elements)
	{
		if (is_array($elements))
		{
			return $elements;
			// throw new \Exception('gestire quando è un array: ' . json_encode($elements));
		}

		if ($elements instanceof Collection)
		{
			if (($elements[0] ?? false) instanceof Model)
				return ($elements->pluck($elements[0]->getKeyName()));

			return $elements;
		}

		return $elements;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getRenderType()
	{
		if ($this->viewName)
			return $this->viewName;

		if (empty($this->renderAs))
			return $this->type;
	}

	public function getDataAttributes()
	{
		return $this->data;
	}

	public function getContainerId()
	{
		if ($this->containerId)
			return $this->containerId;

		$pieces = [];

		if ($this->form ?? false)
			$pieces[] = $this->form->getId();

		$pieces[] = $this->getId();
		$pieces[] = 'container';

		return implode("-", $pieces);
	}

	public function getId()
	{
		if ($this->id)
			return $this->id;

		if ($this->id === false)
			return false;

		$this->id = $this->id ?? Str::slug($this->name);

		if (preg_match('/^\d/', $this->id) === 1)
			$this->id = 'id-' . $this->id;

		if ($model = $this->getModel())
			$this->id = $this->id . $model->getKey();

		return $this->id;
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

	public function isVertical() : bool
	{
		return $this->vertical;
	}

	public function getVerticalClass() : string
	{
		if($this->isVertical())
			return 'ibfieldvertical';

		return 'ibfieldhorizontal';
	}

	public function getHtmlRowClassesString() : string
	{
		$pieces = $this->rowHtmlClasses;
		$pieces[] = $this->getWidthClass();
		$pieces[] = $this->getVerticalClass();
		$pieces[] = Str::slug($this->getName());

		return implode(" ", $pieces);
	}

	public function getWidthClass()
	{
		return $this->widthClass;
	}

	public function getName() : ?string
	{
		return $this->name;
	}

	public function getHtmlLabelClassesString() : string
	{
		return " " . implode(" ", $this->labelHtmlClasses) . " ";
	}

	public function getInputSizeClass() : string
	{
		return $this->inputSizeClass;
	}

	public function getFetcherFieldClasses()
	{
		if (! isset($this->fetcher))
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