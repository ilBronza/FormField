<?php

namespace IlBronza\FormField;

class DatabaseField
{
	public $name;
	public $type;
	public $nullable;
	public $key;
	public $default;
	public $extra;

	public function __construct(object $parameters)
	{
		$this->name = $parameters->Field ?? null;
		$this->type = $parameters->Type ?? null;
		$this->nullable = $parameters->Null ?? null;
		$this->key = $parameters->Key ?? null;
		$this->default = $parameters->Default ?? null;
		$this->extra = $parameters->Extra ?? null;
	}

	public function getDefaultValue()
	{
		return $this->default;
	}
}

