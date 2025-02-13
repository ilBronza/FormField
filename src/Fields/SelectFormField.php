<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\Fields\ListValueFormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\ListValueFormFieldTrait;
use IlBronza\FormField\Traits\NEWMultipleValueFormFieldTrait;
use IlBronza\FormField\Traits\RelationshipFormFieldTrait;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;
use Illuminate\Support\Str;

use function dd;
use function explode;
use function in_array;
use function is_array;

class SelectFormField extends FormField implements FormFieldInterface, ListValueFormFieldInterface, RelatedFormFieldInterface
{
	protected $relationType;
	public $relation;
	protected $possibleValuesArray;
	public $select2 = true;
	public $manualInput;

	public $mustBeSorted = true;

	public $htmlClasses = [
			'uk-select'
		];

	use SingleValueFormFieldTrait;
	use ListValueFormFieldTrait;
	use RelationshipFormFieldTrait;
	use NEWMultipleValueFormFieldTrait;

	private function checkIfModelUsesRelationshipTrait($model)
	{
		if(! in_array('IlBronza\CRUD\Traits\Model\CRUDModelTrait', class_uses($model)))
			throw new \Exception('add ASD CRUDModelTrait to model ' . class_basename($model));
	}

	static public function renderValueForView($value) : ? string
	{
		if(is_string($value))
			return $value;

		if(is_array($value))
			return implode("<br />", $value);

		if(is_null($value))
			return $value;

		return json_encode($value);

		throw new \Exception("Value type not considered jet: " . gettype($value));
	}

	public function hasManualInput()
	{
		return !! $this->manualInput;
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

		if(is_int($value))
			return [$value];

		try
		{
			if(! is_array($value))
				return $value->toArray();
		}
		catch(\Throwable $e)
		{
			return $value;
		}


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

	public function mustBeSorted() : bool
	{
		return !! $this->mustBeSorted;
	}

	public function getPossibleValuesArrayByCastable($cast) : ? array
	{
		if(strpos($cast, 'ExtraFieldDossier') !== false)
		{
			$pieces = explode(':', $cast);
			$_fields = explode(',', $pieces[1]);

			$formrow = Formrow::gpc()::findCachedByField('slug', $_fields[1]);

			return $formrow->getRowType()->getPossibleValuesArray();
		}

		return null;
	}

	public function getPossibleValuesArray()
	{
		if(is_array($this->possibleValuesArray))
			return $this->possibleValuesArray;

		if(isset($this->list))
			return $this->list;

		$model = $this->getModel();

		$casts = $model->getCasts();

		if($cast = ($casts[$this->getName()] ?? null))
			if(($result = $this->getPossibleValuesArrayByCastable($cast)) !== null)
				return $result;

		if(! $model)
			throw new \Exception('Assign a model or declare possibleValuesArray or list');

		if($relationshipName = $this->getRelationshipName())
		{
			$result = $model->getRelationshipPossibleValuesArray(
				$relationshipName
			);

			if(! is_null($result))
			{
				if($this->mustBeSorted())
					asort($result);

				return $result;
			}
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

