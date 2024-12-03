<?php

namespace IlBronza\FormField\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface FormfieldModelCompatibilityInterface
{
	public function getFormfieldType() : string;
	public function getFormfieldValue() : mixed;
	public function getFormfieldName() : string;
	public function getFormfieldPlaceholder(Model $model) : ? string;
	public function getFormfieldLabel() : string;
	public function isFormfieldRequired() : bool;
	public function isFormfieldDisabled() : bool;
	public function getFormfieldRules() : array;
	public function isFormfieldMultiple() : bool;
	public function getFormfieldRelationName() : ? string;
	public function getFormfieldRoles() : ? array;
	public function getFormfieldRepeatable() : bool;
	public function getFormfieldTranslatedTooltip() : ? string;
	public function getFormfieldProblems() : array;
}