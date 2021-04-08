<?php

namespace IlBronza\FormField\Traits;

use \IlBronza\Form\Form;

trait RelationshipFormFieldTrait
{
	public function getRelationshipName()
	{
		return $this->relation ?? false;
	}
}