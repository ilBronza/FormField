<?php

namespace IlBronza\FormField;

use IlBronza\CRUD\Helpers\PackagesHelpers\PackageClassesResolverHelper;
use IlBronza\CRUD\Traits\ElementRolesVisibilityTrait;
use IlBronza\FormField\Traits\FormFieldAlert;
use IlBronza\FormField\Traits\FormFieldChecker;
use IlBronza\FormField\Traits\FormFieldDisplay;
use IlBronza\FormField\Traits\FormFieldGetter;
use IlBronza\FormField\Traits\FormFieldOpener;
use IlBronza\FormField\Traits\FormFieldSetter;
use IlBronza\FormField\Traits\MultipleValueFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

use function strpos;
use function ucfirst;

abstract class FormField
{
	use FormFieldAlert;
	use FormFieldDisplay;
	use FormFieldChecker;
	use FormFieldGetter;
	use FormFieldSetter;
	use FormFieldOpener;
	use ElementRolesVisibilityTrait;

	public bool $dblClickCopy = true;
	public ? string $translationPrefix;
	public ? int $max;
	public ? int $min;
	public $name;
	public $value;
	public $oldName;
	public $default;

	public $problems = [];

	public string $widthClass = 'uk-width-1-1';
	public bool $vertical = false;

	public $type;
	public $form;
	public $fieldset;
	public $parent;
	public $model;
	public $modelClass;

	public $alerts = [];
	public $viewName;
	public $updateEditor;

	public $id;
	public $containerId = false;

	public $multiple = false;

	public ? bool $repeatable = null;
	public ? bool $lastOfType = null;

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

	public array $fetchFieldValue = [];

	public function __construct(array $parameters = [])
	{
		$this->assignArrayParameters($parameters);
	}

	abstract static public function renderValueForView($value) : ? string;

	static function createFromArray(array $parameters)
	{
		$type = $parameters['type'] ?? config('formfield.defaultFieldType');

		if(strpos($type, "::") !== false)
			$formFieldClassName = PackageClassesResolverHelper::getPackageClassNameByType($type);

		else
			$formFieldClassName = __NAMESPACE__ . '\\Fields\\' . ucfirst($type);

		$formFieldClassName .= 'FormField';

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

		$viewName = $this->getViewName($type);

		return view($viewName, ['field' => $this]);
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
		
		return view($this->getShowViewName($type), ['field' => $this]);
	}

	public function transformValueBeforeStore($value)
	{
		return $value;
	}

	public function executeBeforeRenderingOperations() { }

	public function getViewName($type) : string
	{
		if ($this->getDisplayMode() == 'show')
			return $this->getShowViewName($type);

		return "formfield::uikit._{$type}";
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	public function getShowViewName($type) : string
	{
		return "formfield::uikit.show._{$type}";
	}
}

