<?php

namespace IlBronza\FormField\Helpers\FormFieldsProvider;

use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;

use function dd;
use function in_array;
use function pow;
use function stripos;

class FormfieldParametersHelper
{
	static function extractFromModel(FormfieldModelCompatibilityInterface $model) : array
	{
		$result = [
			'type' => $type = $model->getFormfieldType(),
			'value' => $model->getFormfieldValue(),
			'name' => $model->getFormfieldName(),
			'placeholder' => $model->getFormfieldPlaceholder($model),
			'label' => $model->getFormfieldLabel(),
			'required' => $model->isFormfieldRequired(),
			'disabled' => $model->isFormfieldDisabled(),
			'rules' => static::getValidationRulesFromModel($model),
			'multiple' => $model->isFormfieldMultiple($model),
			'relation' => $model->getFormfieldRelationName(),
			'repeatable' => $model->getFormfieldRepeatable(),
			'translatedTooltip' => $model->getFormfieldTranslatedTooltip(),
			'roles' => $model->getFormfieldRoles(),
		];

		if($result['type'] == 'number')
		{
			if(in_array('integer', $result['rules']))
			{
				$result['step'] = 1;
				$result['decimals'] = 0;
			}
			elseif($decimals = $model->getFormrow()->getSpecialParameter('decimals', null))
			{
				$result['step'] = pow(10,  - $decimals);
				$result['decimals'] = $decimals;
			}
		}

		if($model->getRowType()->hasValuesList())
			$result['possibleValuesArray'] = $model->getRowType()->getPossibleValuesArray($model);

		return $result;
	}

	static function getValidationRulesFromModel(FormfieldModelCompatibilityInterface $model) : array
	{
		return $model->getFormfieldRules();
	}
}