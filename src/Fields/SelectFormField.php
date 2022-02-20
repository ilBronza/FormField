<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\Fields\ListValueFormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\ListValueFormFieldTrait;
use IlBronza\FormField\Traits\RelationshipFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;
use Illuminate\Support\Str;

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
		if(! in_array('IlBronza\CRUD\Traits\Model\CRUDModelTrait', class_uses($model)))
			throw new \Exception('add ASD CRUDModelTrait to model ' . class_basename($model));
	}

	public function hasManualInput()
	{
		return false;
	}

	// private function getRelationType()
	// {
	// 	$type = get_class($this->form->model->{$this->relation}());
	// 	return $type;
	// }

	public function getFormOldSelected()
	{
		if(! $this->isMultiple())
		{
			if(is_array($result = $this->getFormOldValue()))
				return $result;

			if($result)
				return [$result];

			if(isset($this->default))
			{
				if(is_array($this->default))
					return $this->default;

				return [$this->default];
			}

			return [];
		}

		$value = $this->getFormOldValue();

		if(! $value)
			return [];

		if(is_string($value))
			return explode(",", $value);

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

	public function getSpecificModelFieldPossibleValues($model)
	{
		$getterMethod = 'getSelectPossible' . Str::studly($this->getName()) . 'Values';

		if(method_exists($model, $getterMethod))
			return $model->{$getterMethod}();

		return ;
	}

	public function getPossibleValuesArray()
	{
		if(is_array($this->possibleValuesArray))
			return $this->possibleValuesArray;

		if(isset($this->list))
			return $this->list;

		$model = $this->getModel();

		if($relationshipName = $this->getRelationshipName())
		{
			$result = $model->getRelationshipPossibleValuesArray(
				$relationshipName
			);

			asort($result);

			return $result;
		}

		if($possibleValues = $this->getSpecificModelFieldPossibleValues($model))
			return $possibleValues;

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

	public function executeBeforeRenderingOperations()
	{
		$this->possibleValuesArray = $this->getPossibleValuesArray();
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

