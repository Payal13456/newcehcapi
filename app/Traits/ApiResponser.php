<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser{

    protected function sendResponse($result, $message){
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
            'messageCode' => 1,
        ];
        return response()->json($response, 200);
    }
    protected function sendError($error, $errorMessages = [], $code = 404){
    	$response = [
            'success' => false,
            'message' => $error,
            'messageCode' => 0,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    protected function errorResponse($message = null, $code)
    {
        return response()->json([
            'status'=>'Error',
            'message' => $message,
            'data' => null
        ], $code);
    }
}