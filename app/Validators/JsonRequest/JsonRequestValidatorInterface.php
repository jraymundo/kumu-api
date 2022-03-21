<?php

namespace App\Validators\JsonRequest;

interface JsonRequestValidatorInterface
{
    /**
     * @return array
     */
    public function rules();

    /**
     * @return array
     */
    public function messages();
}
