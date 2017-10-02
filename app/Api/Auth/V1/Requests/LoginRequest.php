<?php

namespace App\Api\Auth\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.login.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
