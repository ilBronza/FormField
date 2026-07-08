<?php

namespace IlBronza\FormField\Fields;

use function is_string;
use function trim;

class ContractFileFormField extends FileFormField
{
	public ?string $contractUrl = null;
	public ?string $downloadUrl = null;

	public function getContractUrl() : ?string
	{
		$url = $this->contractUrl ?? $this->downloadUrl;

		if (! is_string($url))
			return null;

		$url = trim($url);

		return $url !== '' ? $url : null;
	}

	public function getViewName($type) : string
	{
		if ($this->getDisplayMode() == 'show')
			return $this->getShowViewName($type);

		return 'formfield::uikit._contractFile';
	}

	public function getShowViewName($type) : string
	{
		return 'formfield::uikit.show._contractFile';
	}
}
