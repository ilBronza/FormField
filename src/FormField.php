<?php

namespace IlBronza\FormField;

use IlBronza\CRUD\Traits\ElementRolesVisibilityTrait;
use IlBronza\FormField\Traits\FormFieldChecker;
use IlBronza\FormField\Traits\FormFieldDisplay;
use IlBronza\FormField\Traits\FormFieldGetter;
use IlBronza\FormField\Traits\FormFieldOpener;
use IlBronza\FormField\Traits\FormFieldSetter;
use IlBronza\FormField\Traits\MultipleValueFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;


abstract class FormField
{
	use FormFieldDisplay;
	use FormFieldChecker;
	use FormFieldGetter;
	use FormFieldSetter;
	use FormFieldOpener;
	use ElementRolesVisibilityTrait;

	public $name;
	public $value;
	public $oldName;
	public $default;

	public $type;
	public $form;
	public $fieldset;
	public $parent;
	public $model;
	public $modelClass;
	public $viewName;
	public $updateEditor;

	public $id;
	public $containerId = false;

	public $multiple = false;

	public ? bool $repeatable = null;

	public $visible = true;
	public $closed = false;
	public $readOnly = false;
	public $readOnlyText;
	public $disabled = false;
	public $nullable;

	public $required;
	public $label;
	public $showLabel;
	public $placeholder;
	public $tooltip;

	public $data = [];
	public $htmlClasses = [];
	public $rowHtmlClasses = [];
	public $labelHtmlClasses = [];
	public $inputSizeClass =  'uk-form-small';

	public string $displayMode = 'formfield';
	
	public $rules = [];

	public function __construct(array $parameters = [])
	{
		$this->assignArrayParameters($parameters);
	}

	abstract static public function renderValueForView($value) : ? string;

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

		if($enabledRoles = $parameters['enabledRoles'] ?? false)
		{
			$this->setEnabledRoles($enabledRoles);

			unset($parameters['enabledRoles']);
		}

		foreach($parameters as $name => $value)
			$this->$name = $value;
	}

	public function getDisplayMode() : string
	{
		return $this->displayMode;
	}

	public function render()
	{
		$this->executeBeforeRenderingOperations();

		$type = $this->getRenderType();

		if($this->getDisplayMode() == 'show')
			return view("formfield::uikit.show._{$type}", ['field' => $this]);

		return view("formfield::uikit._{$type}", ['field' => $this]);		
	}

	public function renderPdf()
	{
		$type = $this->getRenderType();

		return view("formfield::uikit.pdf._{$type}", ['field' => $this]);		
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

