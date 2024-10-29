<?php

namespace IlBronza\FormField\Traits;

trait FormFieldAlert
{
	public function hasAlert() : bool
	{
		return count($this->getAlerts());
	}

	public function addAlert(Alert $alert)
	{
		$this->alerts[] = $alert;
	}

	public function getAlerts() : array
	{
		return $this->alerts;
	}

	public function getAlertString(string $separator = "<br />") : ? string
	{
		return implode($separator, $this->getAlerts());
	}
}