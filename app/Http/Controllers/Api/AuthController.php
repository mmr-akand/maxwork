<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Libraries\UserLib\Login\LoginManager;
use App\Http\Controllers\Api\ApiController;
use DB;
use Validator;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $rules =  [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validation_errors = $this->transformErrorMessage($validator);
            return $this->respondValidationError($validation_errors);
        }

    	$email = $request->email;
    	$password = $request->password;

    	$login_manager = new LoginManager();
        $login_user = $login_manager->login( $email, $password );

        if( !$login_user ){
        	return $this->respondErrorInDetails('Sorry! could not login.', $login_manager->getError());
        }

    	$res = $login_manager->complete( $request );

        $this->content['token'] = $res['content']['token'];
    	return response()->json(['data' => $this->content], $res['content']['status']);
    }

    public function logout(Request $request)
    {    
        Auth::user()->token()->revoke();
        return response()->json([
            'data' => [
                'message' => 'Successfully logged out'
            ]
        ]);
    }
}
