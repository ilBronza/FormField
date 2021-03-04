<?php

namespace ilBronza\FormField\Fields;

use Illuminate\Support\Str;
use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\FormField;

class JsonFormField extends FormField implements FormFieldInterface
{
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

	public function __construct()
	{
		parent::__construct();

		$this->innerFields = collect();
	}

	public function getValue()
	{
		if(isset($this->value))
			return $this->value;

		if($this->model)
			return $this->getModelValueByName($this->model, $this->name);

		if(($this->form)&&($this->form->model))
			return $this->getModelValueByName($this->form->model, $this->name);

		if(! empty($this->default))
			return $this->default;

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
		$formField->name = $this->name . '[counter][' . $formField->name . ']';

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
			$innerField->name = str_replace("counter", $key, $innerField->name);
			$innerField->setValue($value[$innerField->subName]);

			$result->push($innerField);
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

		$this->innerFields->push($formField);

		return $formField;
	}
}