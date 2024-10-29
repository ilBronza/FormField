<?php

namespace IlBronza\FormField\Traits;

use function __;

trait FormFieldDisplay
{
	public function getTranslationPrefix() : ? string
	{
		return $this->translationPrefix;
	}

	public function generateContainerId()
	{

	}

	public function getFasIcon()
	{
		if(isset($this->fasIcon))
			return $this->fasIcon;

		return ;
	}

	public function getHtmlClassesString()
	{
		return implode(" ", $this->getHtmlClasses());
	}

	public function getFieldTypeClass()
	{
		return $this->type;
	}

	public function setLabel($label)
	{
		$this->label = $label;
	}

	public function getLabel()
	{
		if($this->showLabel === false)
			return false;

		if($this->label === true)
			return $this->getName();

		else if($this->label)
			return $this->label;

		if(($this->form)&&($this->form->mustShowLabel()))
			return __('fields.' . $this->getName());

		return false;
	}

	public function getPlaceholder()
	{
		if($this->placeholder === true)
			return $this->getName();

		else if($this->translatablePlaceholder ?? false)
			return strip_tags(__('fields.' . $this->translatablePlaceholder)); 

		else if($this->placeholder)
			return strip_tags($this->placeholder);

		return strip_tags(__('fields.' . $this->getName()));
	}

	public function getTooltip()
	{
		if($this->tooltip === true)
			return __($this->getTranslationPrefix() . 'Tooltips.' . $this->getName());

		else if($this->tooltip)
			return __($this->getTranslationPrefix() . 'Tooltips.' . $this->tooltip);

		return false;
	}

	public function getReadOnlyText()
	{
		if($this->readOnlyText === true)
			return trans('formfield::formfield.readOnlyAlertMessage');

		else if($this->readOnlyText)
			return $this->readOnlyText;

		return false;
	}

}