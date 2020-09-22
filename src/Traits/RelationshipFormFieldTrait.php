<?php

namespace ilBronza\FormField\Traits;

use \ilBronza\Form\Form;

trait RelationshipFormFieldTrait
{
	public function getRelationshipName()
	{
		return $this->relation ?? false;
	}
}