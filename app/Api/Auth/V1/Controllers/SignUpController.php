<?php

namespace App\Api\Auth\V1\Controllers;

use Config;
use App\User;
use App\UserType;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\Auth\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser->user_type_id != 1){
            throw new AccessDeniedHttpException();
        }


        $user = new User($request->all());
        if(!$user->save()) {
            throw new HttpException(500);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token' => $token
        ], 201);
    }

    public function users(){
        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser->user_type_id == 1){
            return User::all();
        }
        throw new AccessDeniedHttpException();
    }

    public function user_types(){
        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser->user_type_id == 1){
            return UserType::all();
        }
        throw new AccessDeniedHttpException();
    }

    public function profile(EditUserRequest $request, JWTAuth $JWTAuth)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->save();
        return $user;
    }

    public function test(){
            return User::all();
    }

    public function test2(){
            return UserType::all();
    }
}
