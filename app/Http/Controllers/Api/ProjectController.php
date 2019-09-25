<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use App\Project;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use Validator;
use Hash;

class ProjectController extends ApiController
{
    public function index(Request $request)
    {
        if(Auth::user()->is_admin==1){
            $projects = Project::all();
        }else{
            $projects = Auth::user()->projects;
        }
        return $this->apiResponse([
            'data' => $this->reformCollection($projects)
        ]);
    }

    public function store(Request $request)
    {
        $rules = (new ProjectStoreRequest())->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validation_errors = $this->transformErrorMessage($validator);
            return $this->respondValidationError($validation_errors);
        }

        if(Auth::user()->is_admin==1){
            if(!isset($request->user_id)){
                return $this->respondError('As admin you must provide an user_id parameter');
            }else{
                $user_id = $request->user_id;
            }
        }else{
            $user_id = Auth::user()->id;
        }

        $project_data = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user_id
        ];

        try {
            $project = Project::create($project_data);
        } catch (\Exception $e) {
            return $this->respondError('An error occured');
        }

        return $this->apiResponse([
            'data' => $this->reform($project)
        ]);
    }

    public function update( $id, Request $request )
    {
        $rules = (new ProjectUpdateRequest())->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validation_errors = $this->transformErrorMessage($validator);
            return $this->respondValidationError($validation_errors);
        }

        $project_res = $this->findProject($id);
        if(!$project_res){
            return $this->respondNotFound('Project not found');
        }

        if(Auth::user()->is_admin==0 && $project_res->user_id != Auth::user()->id){
            return $this->respondForbidden('Access denied');
        }

        $filtered_array = array_filter($request->all());
        
        try {
            Project::where('id', $id)->update($filtered_array );
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
        $project_res = $this->findProject($id);
        if(!$project_res){
            return $this->respondNotFound('Project not found');
        }

        if(Auth::user()->is_admin==0 && $project_res->user_id != Auth::user()->id){
            return $this->respondForbidden('Access denied');
        }

        try {
            Project::where('id', $id)->delete();
        } catch (\Exception $e) {
            return $this->respondError('An error occured');
        }

        return $this->apiResponse([
            'data' => [
                'message' => 'Successfully deleted'
            ]
        ]);
    }

    private function findProject($id){
        $project = Project::find($id);
        if(!$project){
            return false;
        }
        return $project;
    }
}
