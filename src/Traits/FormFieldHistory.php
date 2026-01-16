<?php

namespace IlBronza\FormField\Traits;

use IlBronza\CRUD\Helpers\HistoryHelpers\HistoryFinderHelper;

trait FormFieldHistory
{
	public function getHistoryUrl() : ? string
	{
		if(! config('app.usesHistory', false))
			return null;

		if(! $model = $this->getModel())
			return null;

		return HistoryFinderHelper::getHistoryUrlByField($model, $this);
	}
}
