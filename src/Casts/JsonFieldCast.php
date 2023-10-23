<?php

namespace IlBronza\FormField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JsonFieldCast implements CastsAttributes
{
    protected function jsonField($value)
    {
        if(! $value)
            return [];

        if(is_array($value))
            return $value;

        return json_decode($value, true);
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->jsonField($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}