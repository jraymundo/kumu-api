<?php

namespace App\Validators\JsonRequest\Auth;

use App\Validators\JsonRequest\JsonRequestValidatorInterface;

class AuthLoginJsonRequest implements JsonRequestValidatorInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|email:rfc|max:100',
            'password' => 'required|max:100',
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
