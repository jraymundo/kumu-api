<?php

namespace App\Validators\JsonRequest\Github;

use App\Validators\JsonRequest\JsonRequestValidatorInterface;

class GithubUserJsonRequest implements JsonRequestValidatorInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'users' => 'required|array|min:1|max:10',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
