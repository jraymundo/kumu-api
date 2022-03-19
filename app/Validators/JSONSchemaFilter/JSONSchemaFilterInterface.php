<?php

namespace App\Validators\JSONSchemaFilter;

interface JSONSchemaFilterInterface
{
    /**
     * @return array
     */
    public function fields(): array;

    /**
     * @param array $attributes
     * @return array
     */
    public function filter(array $attributes): array ;
}
