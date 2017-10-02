<?php

namespace App\Api\Auth\V1\Controllers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\Auth\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginController extends Controller
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
