<?php

namespace IlBronza\FormField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JsonFieldCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return decrypt($value);
    }

    public function set($model, $key, $value, $attributes)
    {
        return [$key => encrypt($value)];
    }
}