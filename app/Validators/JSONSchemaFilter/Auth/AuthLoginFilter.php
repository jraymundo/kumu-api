<?php

namespace App\Validators\JSONSchemaFilter\Auth;

use App\Validators\JSONSchemaFilter\JSONSchemaFilterInterface;

class AuthLoginFilter implements JSONSchemaFilterInterface
{
    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'username',
            'password'
        ];
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function filter(array $attributes): array
    {
        return array_intersect_key($attributes, array_flip($this->fields()));
    }
}
