<?php

namespace IlBronza\FormField\Fields;

class TexteditorFormField extends TextareaFormField
{
	public bool $vertical = true;

	public $htmlClasses = [
			'uk-textarea',
			'texteditor'
		];
}