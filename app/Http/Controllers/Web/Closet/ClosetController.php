<?php

namespace App\Http\Controllers\Web\Closet;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Closet;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ClosetController extends Controller
{
    /**
     * Show the application closet orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            return view('closet.closet.index');
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }


    /**
     * Get list of the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getListingRecord(Request $request)
    {
        try {
            $filter = $request->all();
            $usersRecord = Closet::getByFilters($filter);
            $response = $this->makeDatatable($usersRecord);
            return $response;
        } catch (\Exception $e) {
            AppException::log($e);
            dd($e->getTraceAsString());
        }
    }

    private function makeDatatable($data)
    {
        return DataTables::of($data['records'])
            ->addColumn('check', function ($rowdata) {
                $disabled = '';
                if (!empty($rowdata->deleted_at))
                {
                    $disabled = 'disabled="disabled"';
                }
                return '<input type="checkbox" ' . $disabled . ' name="data_raw_id[]"  class="theClass" value="' . $rowdata->id . '">';
            })
            ->addColumn('closet_name', function ($rowdata) {
                $disabledClass = "";
                $url = url("/closets/" . $rowdata->id.'/edit');
                $target = "_blank";
                return '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->closet_name . '</a>';
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = $rowdata->status;
                $userStatus = array_flip(Constant::USER_STATUS);
                return '<label class="badge badge-' . Constant::USER_STATUS_STYLE[$isActive] . '"> ' . $userStatus[$isActive] . '</label>';
            })
            ->addColumn('created_at', function ($rowdata) {
//                optional($rowdata->created_record)->name
                return Helper::dated_by(null,$rowdata->created_at);
            })
            ->addColumn('updated_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata->updated_at);
            })
            ->rawColumns(['check', 'name', 'user_type', 'status','created_at','updated_at'])
            ->setOffset($data['offset'])
            ->with([
                "recordsTotal" => $data['count'],
                "recordsFiltered" => $data['count'],
            ])
            ->setTotalRecords($data['count'])
            ->make(true);
    }

}
