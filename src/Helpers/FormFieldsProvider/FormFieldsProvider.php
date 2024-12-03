<?php

namespace IlBronza\FormField\Helpers\FormFieldsProvider;

use IlBronza\FormField\FormField;

use function in_array;

class FormFieldsProvider
{
	static ?string $translationPrefix;

	static function createByNameParameters(string $name, array $parameters) : FormField
	{
		$parameters = static::manageParameters($name, $parameters);

		return FormField::createFromArray($parameters);
	}

	static function manageParameters(string $name, array $parameters) : array
	{
		$parameters = static::sanitizeParameters($parameters);
		$parameters = static::buildName($name, $parameters);

		$parameters = static::buildLabel($parameters);

		return static::buildParametersRules($parameters);
	}

	static function sanitizeParameters(array $parameters)
	{
		if (count($parameters) == 1)
			return static::getFieldParametersBySingleRow($parameters);

		return static::getFieldParametersKeyValueRow($parameters);
	}

	static function getFieldParametersBySingleRow(array $parametersString)
	{
		$type = array_key_first($parametersString);
		$parameters['type'] = $type;

		$rules = explode("|", $parametersString[$type]);
		$parameters['rules'] = $rules;

		return $parameters;
	}

	static function getFieldParametersKeyValueRow(array $parametersString)
	{
		if (! isset($parametersString['type']))
			throw new \Exception('Missing "type" array element in field ' . $name);

		if (! isset($parametersString['rules']))
			throw new \Exception('Missing "rules" array element in field ' . $name);

		$parameters = $parametersString;

		return $parameters;
	}

	static function buildName(string $name, array $parameters) : array
	{
		if (! ($parameters['name'] ?? false))
			$parameters['name'] = $name;

		return $parameters;
	}

	static function buildParametersRules($parameters)
	{
		$rules = $parameters['rules'];

		if (is_string($rules))
			$rules = explode("|", $parameters['rules']);

		if (! isset($parameters['required']))
			$parameters['required'] = static::checkIfFieldIsRequired($rules);

		$parameters['rules'] = [];

		foreach ($rules as $key => $rule)
			if (strpos($rule, ":"))
			{
				$_rule = explode(":", $rule);

				$parameters['rules'][$_rule[0]] = $_rule[1];

				if ($_rule[0] == 'max')
					$parameters['max'] = $_rule[1];
			}
			else
				$parameters['rules'][$rule] = true;

		return $parameters;
	}

	static function checkIfFieldIsRequired(array $rules) : bool
	{
		return in_array('required', $rules);
	}

	static function buildLabel(array $parameters) : array
	{
		if (! ($parameters['label'] ?? false))
			$parameters['label'] = trans(static::getTranslationPrefix($parameters) . '.' . $parameters['name']);

		return $parameters;
	}

	static function getTranslationPrefix(array $parameters) : string
	{
		if ($parameters['translationPrefix'] ?? false)
			return $parameters['translationPrefix'];

		return static::$translationPrefix ?? 'fields';
	}

	public function setParameters(array $parameters) {}

	// static function getParametersByArray(array $parameters) : array
	// {
	// 	$FormFieldsProvider = new static();

	// 	$FormFieldsProvider->setParameters($parameters);

	// }
}