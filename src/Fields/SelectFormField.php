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

	use SingleValueFormFieldTrait;
	use ListValueFormFieldTrait;
	use RelationshipFormFieldTrait;

	private function checkIfModelUsesRelationshipTrait($model)
	{
		if(! in_array('ilBronza\CRUD\Traits\Model\CRUDModelTrait', class_uses($model)))
			throw new \Exception('add ASD CRUDModelTrait to model ' . class_basename($model));
	}

	// private function getRelationType()
	// {
	// 	$type = get_class($this->form->model->{$this->relation}());
	// 	return $type;
	// }

	public function getFormOldSelected()
	{
		if(! $this->isMultiple())
			return [$this->getFormOldValue()];

		$value = $this->getFormOldValue();

		if(! is_array($value))
			return $value->toArray();

		return $value;
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

		$relationshipName = $this->getRelationshipName();

		return $model->getRelationshipPossibleValuesArray(
			$relationshipName
		);
	}

	// public $nullableValues = ['true' => 1, 'false' => 0, 'null' => null];
	// public $compulsoryValues = ['true' => 1, 'false' => 0];

	// public function getPossibleValues()
	// {
	// 	if($this->isNullable())
	// 		return $this->getNullableValues();

	// 	return $this->getCompulsoryValues();
	// }

	// private function getNullableValues()
	// {
	// 	return $this->nullableValues;
	// }

	// private function getCompulsoryValues()
	// {
	// 	return $this->compulsoryValues;
	// }
}

