<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use App\Enumarations\ApiErrorCodes;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $status_code = 200;

    public function getStatusCode()
    {
        return $this->status_code;
    }
    
    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function apiResponse($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function apiErrorResponse($message, $error_details = false)
    {
        if ($error_details) {

            $error['message'] = isset($error_details['message']) ? $error_details['message'] :  $message;

            $error['type'] = $error_details['type'];

            if ($error_details['type'] == 'validation_error') {
                $error['error_details']['error_code'] = ApiErrorCodes::$VALIDATION_ERROR;
                $error['error_details']['error_title'] = 'Validation error';
                $error['error_details']['error_feild'] = null;
                $error['error_details']['validation_errors'] = $error_details['validation_errors'];
            }else if ($error_details['type'] == 'incorrect_login') {
                $error['error_details']['error_code'] = ApiErrorCodes::$INVALID_LOGIN;
                $error['error_details']['error_title'] = 'Invalid login';
                $error['error_details']['error_feild'] = null;
            }else if ($error_details['type'] == 'blocked_user') {
                $error['error_details']['error_code'] = ApiErrorCodes::$BLOCKED_USER;
                $error['error_details']['error_title'] = 'Blocked user';
                $error['error_details']['error_feild'] = null;
            }else if ($error_details['type'] == 'inactive_user') {
                $error['error_details']['error_code'] = ApiErrorCodes::$INACTIVE_USER;
                $error['error_details']['error_title'] = 'Inactive user';
                $error['error_details']['error_feild'] = null;
            }else if ($error_details['type'] == 'unverified_phone') {
                $error['error_details']['error_code'] = ApiErrorCodes::$UNVERIFIED_PHONE;
                $error['error_details']['error_title'] = 'Unverified phone';
                $error['error_details']['error_feild'] = null;
            }else if ($error_details['type'] == 'panel_mismatch') {
                $error['error_details']['error_code'] = ApiErrorCodes::$PANEL_MISMATCH;
                $error['error_details']['error_title'] = 'Panel mismatch';
                $error['error_details']['error_feild'] = null;
            }
            unset($error['error_details']['type']);
        } else {
            $error['message'] = $message;
            $error['type'] = null;
            $error['error_details'] = null;
        }

        return $this->apiResponse([
            'error' => $error
        ]);
    }
    
    public function respondError($message = 'Bad Request')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->apiErrorResponse($message);
    }

    public function respondErrorInDetails($message, $error_details)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->apiErrorResponse($message, $error_details);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->apiErrorResponse($message);
    }

    public function respondForbidden($message = 'Unautohrized')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)->apiErrorResponse($message);
    }

    public function respondValidationError($fields, $message = 'Please correct the errors in the form!')
    {
        $error_details = [
            'type' => 'validation_error',
            'validation_errors' => $fields
        ];

        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->apiErrorResponse($message, $error_details);
    }

    /**
     * Reform a eloquent object
     */
    public function reform($item)
    {
        return $item->getApiModel();
    }

    /**
     * Reform a collection
     */
    public function reformCollection($collection)
    {
        $collection->transform(function ($item, $key) {
            return $this->reform($item);
        });

        return $collection;
    }


    public function transformErrorMessage($validator)
    {
        $validation_errors = $validator->errors()->messages();

        $error_messages = [];
        foreach($validation_errors as $key => $msg) {
            $error_messages[] = [
                'field' => $key,
                'errors' => $msg
            ];
        }

        return $error_messages;
    }
}
