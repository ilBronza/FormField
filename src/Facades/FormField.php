<?php

namespace IlBronza\FormField\Facades;

use Illuminate\Support\Facades\Facade;

class FormField extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'formfield';
    }
}
