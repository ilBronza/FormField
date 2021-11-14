<?php

namespace IlBronza\FormField\Fields;

use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FileFormField extends FormField implements FormFieldInterface
{
	use SingleValueFormFieldTrait;

	public $dropzone = true;

	public $htmlClasses = [
		];

	public function isDropzone()
	{
		return $this->dropzone;
	}

	public function loadModelMediaCollections(Model $model)
	{
		if(isset($model->crudMediaCollections))
			return $model->crudMediaCollections;

		$model->crudMediaCollections = $model->media->groupBy('collection_name');

		return $this->loadModelMediaCollections($model);
	}

	public function getFiles() : Collection
	{
		if(! $model = $this->getModel())
			return collect();

		if(! $this->modelHasMedia($model))
			return collect();

		if(! $media = $this->loadModelMediaCollections($model))
			return collect();

		return $media[$this->name] ?? collect();
	}

	public function getMethod()
	{
		if($this->form->method)
			return $this->form->method;

		throw new \Exception('Gestire method di diversa provenienza se in mancanza di form');
	}

	public function getUploadingUrl()
	{
		if($this->form->action)
			return $this->form->action;

		throw new \Exception('Gestire url di diversa provenienza se in mancanza di form');
	}


	/**
	 * check if model interacts with spatie medialibrary
	 *
	 * @return boolean
	 **/
	public function modelHasMedia(Model $model) : bool
	{
		return in_array("Spatie\\MediaLibrary\\InteractsWithMedia", class_uses_recursive($model));
	}
}