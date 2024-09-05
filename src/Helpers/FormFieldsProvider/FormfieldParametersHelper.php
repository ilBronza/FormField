<?php

namespace IlBronza\FormField\Helpers\FormFieldsProvider;

use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;

class FormfieldParametersHelper
{
	static function extractFromModel(FormfieldModelCompatibilityInterface $model) : array
	{
		$result = [
			'type' => $model->getFormfieldType(),
			'value' => $model->getFormfieldValue(),
			'name' => $model->getFormfieldName(),
			'placeholder' => $model->getFormfieldLabel(),
			'label' => $model->getFormfieldLabel(),
			'required' => $model->isFormfieldRequired(),
			'disabled' => $model->isFormfieldDisabled(),
			'rules' => static::getValidationRulesFromModel($model),
			'multiple' => $model->isFormfieldMultiple(),
			'relation' => $model->getFormfieldRelationName(),
			'repeatable' => $model->getFormfieldRepeatable(),
			'roles' => $model->getFormfieldRoles(),
		];

		if($model->getRowType()->hasValuesList())
			$result['possibleValuesArray'] = $model->getRowType()->getPossibleValuesArray();

		return $result;
	}

	static function getValidationRulesFromModel(FormfieldModelCompatibilityInterface $model) : array
	{
		return $model->getFormfieldRules();
	}
}