<?php

namespace App\Http\Controllers\Web\Closet;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PIMController extends Controller
{
    /**
     * Show the application closet product inventory.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            return view('closet.pim.index');
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
