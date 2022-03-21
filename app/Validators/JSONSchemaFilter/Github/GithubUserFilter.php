<?php

namespace App\Validators\JSONSchemaFilter\Github;

use App\Validators\JSONSchemaFilter\JSONSchemaFilterInterface;

class GithubUserFilter implements JSONSchemaFilterInterface
{
    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'users',
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
