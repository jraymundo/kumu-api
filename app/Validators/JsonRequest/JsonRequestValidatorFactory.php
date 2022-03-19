<?php

namespace App\Validators\JsonRequest;

class JsonRequestValidatorFactory
{
    /**
     * @param $key
     * @return mixed
     */
    public static function make($key)
    {
        $list = [
            'App\Http\Controllers\Api\AuthController@login_post' => 'App\Validators\JsonRequest\Auth\AuthLoginJsonRequest',
            'App\Http\Controllers\Api\AuthController@register_post' => 'App\Validators\JsonRequest\Auth\AuthRegisterJsonRequest',
            'App\Http\Controllers\Api\GithubController@getUsers_post' => 'App\Validators\JsonRequest\Github\GithubUserJsonRequest',
        ];

        return (new $list[$key]);
    }
}
