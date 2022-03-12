<?php

namespace IlBronza\FormField\Fields;

class RadioFormField extends SelectFormField
{
	public $stacked = true;

	public $mustTranslateLabel = true;

	public $htmlClasses = [
			'uk-radio'
		];

	public function getHtmlClasses()
	{
		return $this->htmlClasses;
	}

	public function isStacked()
	{
		return $this->stacked;
	}

	public function getStackingClass()
	{
		if($this->isStacked())
			return ' uk-display-block ';

		return null;
	}

	public function mustTranslateLabel()
	{
		return $this->mustTranslateLabel;
	}

	public function getLabelByIndex(string $index)
	{
		if($this->mustTranslateLabel())
			return __('fields.checkboxLabel' . $index);

		return $index;
	}
}

