<?php

namespace IlBronza\FormField\Fields;

use Illuminate\Support\Str;
use IlBronza\CRUD\Traits\CRUDArrayFieldsTrait;
use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;

class JsonFormField extends FormField implements FormFieldInterface
{
	public $position = true;
	public $vertical = false;

	use CRUDArrayFieldsTrait;

	// use SingleValueFormFieldTrait;
	// use ListValueFormFieldTrait;
	// use RelationshipFormFieldTrait;

	// public $htmlClasses = [
	// 		'uk-input'
	// 	];

	// 'price_coefficient' => [
	//     'type' => 'json',
	//     'fields' => [
	//         'quantity' => ['number' => 'integer|required|min:1'],
	//         'coefficient' => ['number' => 'numeric|required|min:0']
	//     ],
	//     'rules' => 'array|required',
	// ],

	public $innerFields;
	public $showLabels = false;

	public function __construct(array $parameters = [])
	{
		parent::__construct($parameters);

		$this->manageOrientation();

		$this->innerFields = collect();

		foreach($parameters['fields'] as $fieldName => $field)
			$this->addFormField(
				FormField::createFromArray(
					$this->getFieldParameters($fieldName, $field)
				)
			);
	}

	public function manageOrientation() : void
	{
		if(! $this->isVertical())
			return ;

		$this->showLabels = true;
	}

	public function isVertical() : bool
	{
		return !! $this->vertical;
	}

	public function hasPosition()
	{
		return $this->position;
	}

	public function transformValueByPosition(array $value) : array
	{
		return $value;
		if(! $this->hasPosition())
			return $value;

		ksort($value);

		return array_values($value);
	}

	public function getValue()
	{
		if(isset($this->value))
			return $this->transformValueByPosition(
				$this->value
			);

		if($this->model)
			return $this->transformValueByPosition(
				$this->getModelValueByName($this->model, $this->name)
			);

		if(($this->form)&&($this->form->model))
			return $this->transformValueByPosition(
				$this->getModelValueByName($this->form->model, $this->name)
			);

		if(! empty($this->default))
			return $this->transformValueByPosition(
				$this->default
			);

		return null;
		// throw new \Exception('Nessun model da dove prendere il dato');
	}

	public function getFormOldValue()
	{
		$value = old(
			$this->getFormOldName(),
			$this->getValue()
		);

		if(! $value)
			return [];

		if(is_string($value))
			return json_decode($value, true);

		return $value;
	}

	private function manageLabel(FormField $formField) : FormField
	{
		if($this->showLabels == false)
			$formField->setLabel(false);

		return $formField;
	}

	private function manageForm(FormField $formField) : FormField
	{
		if(! $formField->form)
			$formField->form = $this->form;

		return $formField;
	}

	private function manageName(FormField $formField) : FormField
	{
		$formField->subName = $formField->name;
		$formField->name = $this->name . '[][' . $formField->name . ']';

		return $formField;
	}

	private function managePlaceholder(FormField $formField) : FormField
	{
		if(! $formField->placeholder)
			$formField->placeholder = __('fields.' . Str::slug($formField->getName(), '_'));

		return $formField;
	}

	private function removeId(FormField $formField) : FormField
	{
		$formField->setId(false);

		return $formField;
	}

	public function getInnerFieldsByKeyValue(string $key, array $value)
	{
		$result = collect();

		foreach($this->innerFields as $innerField)
		{
			$_innerField = clone $innerField;

			// $_innerField->name = str_replace("counter", "", $_innerField->name);
			$_innerField->setValue($value[$_innerField->subName] ?? null);

			$result->push($_innerField);
		}

		return $result;
	}

	public function addFormField(FormField $formField) : FormField
	{
		$formField = $this->manageLabel($formField);
		$formField = $this->manageForm($formField);
		$formField = $this->managePlaceholder($formField);
		$formField = $this->manageName($formField);
		$formField = $this->removeId($formField);

		$formField->addRowHtmlClass('uk-width-1-1');

		$this->innerFields->push($formField);

		return $formField;
	}
}