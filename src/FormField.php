<?php

namespace ilBronza\FormField;

use ilBronza\FormField\Traits\FormFieldChecker;
use ilBronza\FormField\Traits\FormFieldDisplay;
use ilBronza\FormField\Traits\FormFieldGetter;
use ilBronza\FormField\Traits\FormFieldSetter;
use ilBronza\FormField\Traits\MultipleValueFormFieldTrait;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;


class FormField
{
	use FormFieldDisplay;
	use FormFieldChecker;
	use FormFieldGetter;
	use FormFieldSetter;

	public $name;
	public $oldName;

	public $type;
	public $form;
	public $model;
	public $modelClass;

	public $id;
	public $containerId = false;

	public $multiple = false;

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
	public $rules = [];

	public function __construct()
	{
		
	}

	static function createFromArray(array $parameters)
	{
		$type = $parameters['type'] ?? config('formfield.defaultFieldType');

		$formFieldClassName = __NAMESPACE__ . '\\Fields\\' . ucfirst($type) . 'FormField';

		return $formFieldClassName::_createFromArray($parameters);
	}

	static function _createFromArray(array $parameters)
	{
		$field = new static();

		$field->assignArrayParameters($parameters);

		return $field;
	}

	public function assignArrayParameters(array $parameters)
	{
		foreach($parameters as $name => $value)
			$this->$name = $value;
	}

	public function render()
	{
		$type = $this->getRenderType();

		return view("formfield::uikit._{$type}", ['field' => $this]);
	}
}

