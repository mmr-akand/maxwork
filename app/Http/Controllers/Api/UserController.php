<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Validator;
use Hash;

class UserController extends ApiController
{
    public function index(Request $request)
    {
        $users = User::all();
        return $this->apiResponse([
            'data' => $this->reformCollection($users)
        ]);
    }

    public function store(Request $request)
    {
        $rules = (new UserStoreRequest())->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validation_errors = $this->transformErrorMessage($validator);
            return $this->respondValidationError($validation_errors);
        }

        $user_data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin,
        ];

        try {
            $user = User::create($user_data);
        } catch (\Exception $e) {
            return $this->respondError('An error occured');
        }

        return $this->apiResponse([
            'data' => $this->reform($user)
        ]);
    }

    public function update( $id, Request $request )
    {
        $rules = (new UserUpdateRequest())->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validation_errors = $this->transformErrorMessage($validator);
            return $this->respondValidationError($validation_errors);
        }

        $user_res = $this->findUser($id);
        if(!$user_res){
            return $this->respondNotFound('User not found');
        }

        if(Auth::user()->is_admin==0 && $user_res->id != Auth::user()->id){
            return $this->respondForbidden('Access denied');
        }
        
        $filtered_array = array_filter($request->all());
        if(isset($filtered_array['password'])){
            $filtered_array['password'] = bcrypt($filtered_array['password']);
        }

        if(isset($filtered_array['email'])){
            $match_mail_res = $this->matchMailToOtherUser($id, $filtered_array['email']);
            if($match_mail_res){
                return $this->respondError('Email already exists for another user');
            }
        }

        try {
            User::where('id', $id)->update($filtered_array);
        } catch (\Exception $e) {
            return $this->respondError('An error occured');
        }

        return $this->apiResponse([
            'data' => [
                'message' => 'Successfully updated'
            ]
        ]);

    }

    public function destroy(Request $request, $id)
    {
        $user_res = $this->findUser($id);
        if(!$user_res){
            return $this->respondNotFound('User not found');
        }

        try {
            User::where('id', $id)->delete();
        } catch (\Exception $e) {
            return $this->respondError('An error occured');
        }

        return $this->apiResponse([
            'data' => [
                'message' => 'Successfully deleted'
            ]
        ]);
    }

    private function findUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return false;
        }
        return $user;
    }

    private function matchMailToOtherUser($id, $email)
    {
        $email_existance = User::where('id', '!=', $id)->where('email', $email);
        if($email_existance->exists()){
            return true;
        }
        return false;
    }
}
