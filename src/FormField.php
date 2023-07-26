<?php

namespace IlBronza\FormField;

use IlBronza\FormField\Traits\FormFieldChecker;
use IlBronza\FormField\Traits\FormFieldDisplay;
use IlBronza\FormField\Traits\FormFieldGetter;
use IlBronza\FormField\Traits\FormFieldOpener;
use IlBronza\FormField\Traits\FormFieldSetter;
use IlBronza\FormField\Traits\MultipleValueFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;


class FormField
{
	use FormFieldDisplay;
	use FormFieldChecker;
	use FormFieldGetter;
	use FormFieldSetter;
	use FormFieldOpener;

	public $name;
	public $value;
	public $oldName;
	public $default;

	public $type;
	public $form;
	public $fieldset;
	public $model;
	public $modelClass;
	public $viewName;
	public $updateEditor;

	public $id;
	public $containerId = false;

	public $multiple = false;

	public $visible = true;
	public $closed = false;
	public $readOnly = false;
	public $disabled = false;
	public $nullable;

	public $required;
	public $label;
	public $placeholder;
	public $tooltip;

	public $data = [];
	public $htmlClasses = [];
	public $rowHtmlClasses = [];
	public $labelHtmlClasses = [];
	
	public $rules = [];

	public function __construct(array $parameters = [])
	{
		$this->assignArrayParameters($parameters);
	}

	static function createFromArray(array $parameters)
	{
		$type = $parameters['type'] ?? config('formfield.defaultFieldType');

		$formFieldClassName = __NAMESPACE__ . '\\Fields\\' . ucfirst($type) . 'FormField';

		return $formFieldClassName::_createFromArray($parameters);
	}

	static function _createFromArray(array $parameters)
	{
		return new static($parameters);
	}

	public function assignArrayParameters(array $parameters)
	{
		if($parameters['htmlClasses'] ?? false)
			$parameters['htmlClasses'] = array_merge($parameters['htmlClasses'], $this->htmlClasses);

		foreach($parameters as $name => $value)
			$this->$name = $value;
	}

	public function render()
	{
		$this->executeBeforeRenderingOperations();

		$type = $this->getRenderType();
		
		return view("formfield::uikit._{$type}", ['field' => $this]);
	}

	public function renderShow()
	{
		// $this->executeBeforeRenderingOperations();

		$type = $this->getRenderType();
		
		return view("formfield::uikit.show._{$type}", ['field' => $this]);
	}

	public function transformValueBeforeStore($value)
	{
		return $value;
	}

	public function executeBeforeRenderingOperations() { }
}

