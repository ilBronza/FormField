<?php

namespace IlBronza\FormField\Traits;

use function is_null;

trait FormFieldChecker
{
	public function hasDblClickCopy() : bool
	{
		return ! ! $this->getDblClickCopy();
	}

	public function isLastOfType() : bool
	{
		return ! ! $this->lastOfType;
	}

	public function isFirstOfType() : bool
	{
		return ! ! $this->firstOfType;
	}

	public function isRelationship() : bool
	{
		return ! ! $this->getRelationshipName();
	}

	public function hasFetcher()
	{
		return isset($this->fetcher);
	}

	public function isButton() : bool
	{
		return $this->getType() == 'button';
	}

	public function isClosed()
	{
		return $this->closed;
	}

	public function isVisible() : bool
	{
		return $this->visible;
	}

	public function isReadOnly()
	{
		return $this->readOnly;
	}

	public function isDisabled()
	{
		if ($this->isEnabledForUserRole())
			return false;

		return $this->disabled;
	}

	public function isMultiple()
	{
		return $this->multiple;
	}

	public function isNullable()
	{
		return ! $this->isRequired();
	}

	public function isRequired() : bool
	{
		return ! ! $this->required;
	}

	public function rulesContain(string $rule)
	{
		return in_array($rule, array_keys($this->rules));
	}

	public function hasAutocomplete()
	{
		return false;
	}

	public function mustShowPlaceholder()
	{
		if ((is_null($this->placeholder)) && (isset($this->form)))
			return $this->form->mustShowPlaceholder;

		return ! ! $this->placeholder;
	}

	public function hasUpdateEditor() : bool
	{
		if (is_null($this->updateEditor))
			return ! ! $this->getForm()?->hasUpdateEditor();

		if (! $this->updateEditor)
			return false;

		if (is_array($this->updateEditor))
			return count($this->updateEditor) > 0;

		return $this->updateEditor;
	}

	public function getAjaxDeleteInstanceUrl() : ? string
	{
		if(($this->getModel())&&($this->getModel()->exists))
			return $this->getModel()->getAjaxDeleteInstanceUrl();

		return null;
	}

	public function getUpdateEditorUrl() : ? string
	{
		if (is_array($this->updateEditor))
			return $this->updateEditor['url'];

		if(($this->getModel())&&($this->getModel()->exists))
			return $this->getModel()->getUpdateUrl();

		return null;
	}
}