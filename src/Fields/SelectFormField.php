<?php

namespace ilBronza\FormField\Fields;

use ilBronza\FormField\Fields\FormFieldInterface;
use ilBronza\FormField\Fields\ListValueFormFieldInterface;
use ilBronza\FormField\FormField;
use ilBronza\FormField\Traits\ListValueFormFieldTrait;
use ilBronza\FormField\Traits\RelationshipFormFieldTrait;
use ilBronza\FormField\Traits\SingleValueFormFieldTrait;

class SelectFormField extends FormField implements FormFieldInterface, ListValueFormFieldInterface, RelatedFormFieldInterface
{
	protected $relationType;
	protected $possibleValuesArray;
	public $select2 = true;

	public $htmlClasses = [
			'uk-select'
		];

	use SingleValueFormFieldTrait;
	use ListValueFormFieldTrait;
	use RelationshipFormFieldTrait;

	private function checkIfModelUsesRelationshipTrait($model)
	{
		if(! in_array('ilBronza\CRUD\Traits\Model\CRUDModelTrait', class_uses($model)))
			throw new \Exception('add ASD CRUDModelTrait to model ' . class_basename($model));
	}

	public function hasManualInput()
	{
		if(! isset($this->manualInput))
			return false;

		return $this->manualInput;
	}

	public function multipleConditionIsManaged()
	{
		return ! is_null($this->multiple);
	}

	public function hasValidationForMultiple()
	{
		$multipleFields = array_intersect(['array'], array_keys($this->rules));

		return count($multipleFields) > 0;
	}

	public function manageMultipleCondition()
	{
		if($this->multipleConditionIsManaged())
			return;

		$this->multiple = $this->hasValidationForMultiple();
	}

	public function checkPostCreationParameters()
	{
		$this->manageMultipleCondition();
	}

	public function getFormOldSelected()
	{
		if(! $this->isMultiple())
		{
			if(is_array($result = $this->getFormOldValue()))
				return $result;

			if($result)
				return [$result];

			return [];
		}

		$value = $this->getFormOldValue();

		if(is_array($value))
			return $value;

		if(class_basename($value) == 'Collection')
			return $value->toArray();

		return [$value];
	}

	private function getRelatedFullModelClass()
	{
		return get_class($this->form->model->{$this->relation}()->getRelated());
	}

	private function getRelatedModelClass()
	{
		$pieces = explode("\\", $this->getRelatedFullModelClass());

		return array_pop($pieces);
	}

	public function getPossibleValuesArray()
	{
		if($this->possibleValuesArray)
			return $this->possibleValuesArray;

		$model = $this->getModel();

		if($relationshipName = $this->getRelationshipName())
			return $model->getRelationshipPossibleValuesArray(
				$relationshipName
			);

		return $this->getPossibleEnumValuesArray();
	}

	public function isSelect2()
	{
		return $this->select2;
	}

	public function getHtmlClasses()
	{
		if($this->isSelect2())
			$this->htmlClasses[] = 'select2';

		return $this->htmlClasses;
	}
}

