<?php

namespace IlBronza\FormField\Traits;

use function __;
use function str_replace;

trait FormFieldDisplay
{
	public function generateContainerId() {}

	public function getFasIcon()
	{
		if (isset($this->fasIcon))
			return $this->fasIcon;

		return;
	}

	public function getHtmlClassesString()
	{
		return implode(" ", $this->getHtmlClasses());
	}

	public function getFieldTypeClass()
	{
		return $this->type;
	}

	public function setLabel(string $label) : self
	{
		$this->label = $label;

		return $this;
	}

	public function getLabel()
	{
		if ($this->showLabel === false)
			return false;

		if ($this->label === true)
			return __($this->getTranslationPrefix() . '.' . $this->getCleanedNameForTranslation());

		else if ($this->label)
			return $this->label;

		if (($this->form) && ($this->form->mustShowLabel()))
			return __($this->getTranslationPrefix() . '.' . $this->getCleanedNameForTranslation());

		return false;
	}

	public function getTranslationPrefix() : ?string
	{
		if (isset($this->translationPrefix))
			return $this->translationPrefix;

		return null;
	}

	public function getCleanedNameForTranslation() : string
	{
		$result = trim(str_replace(["[", "]"], " ", $this->getName()));

		return preg_replace('/ +/', '_', $result);
	}

	public function getPlaceholder()
	{
		if ($this->placeholder === true)
			return $this->getName();

		else if ($this->translatablePlaceholder ?? false)
			return strip_tags(__('fields.' . $this->translatablePlaceholder));

		else if ($this->placeholder)
			return strip_tags($this->placeholder);

		return strip_tags(__('fields.' . $this->getName()));
	}

	public function getTooltip()
	{
		if ($tooltip = $this->getTranslatedTooltip())
			return $tooltip;

		if ($this->tooltip === true)
			return __($this->getTranslationPrefix() . 'Tooltips.' . $this->getName());

		else if ($this->tooltip)
			return __($this->getTranslationPrefix() . 'Tooltips.' . $this->tooltip);

		return false;
	}

	public function getTranslatedTooltip() : ?string
	{
		return $this->translatedTooltip;
	}

	public function getReadOnlyText()
	{
		if ($this->readOnlyText === true)
			return trans('formfield::formfield.readOnlyAlertMessage');

		else if ($this->readOnlyText)
			return $this->readOnlyText;

		return false;
	}

}