<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface RepositoryInterface
 * @package App\Repositories
 */
interface EloquentRepositoryInterface
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function getOneByAttributes(array $attributes): ?Model;
}
