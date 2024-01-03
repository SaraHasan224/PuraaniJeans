<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Show the application roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            return view('roles.index');
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
