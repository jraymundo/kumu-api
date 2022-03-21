<?php

namespace App\Validators\JSONSchemaFilter;

class JSONSchemaFilterFactory
{
    /**
     * @param $key
     * @param array $attributes
     * @return array
     */
    public static function make($key, array $attributes)
    {
        $list = [
            'App\Http\Controllers\Api\AuthController@login_post' => 'App\Validators\JSONSchemaFilter\Auth\AuthLoginFilter',
            'App\Http\Controllers\Api\AuthController@register_post' => 'App\Validators\JSONSchemaFilter\Auth\AuthRegisterFilter',
            'App\Http\Controllers\Api\GithubController@getUsers_post' => 'App\Validators\JSONSchemaFilter\Github\GithubUserFilter',
        ];

        return (new $list[$key])->filter($attributes);
    }
}
