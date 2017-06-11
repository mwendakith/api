<?php

namespace App\Api\V1\Controllers\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginController extends BaseController
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);
        $token;

        try {
            $token = $JWTAuth->attempt($credentials);

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        if($token){
            return response()
                ->json([
                    'status' => 'ok',
                    'token' => $token
                ]);
        }
        else{
            throw new AccessDeniedHttpException();
        }

        
    }
}
