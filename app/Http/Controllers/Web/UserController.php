<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Yajra\DataTables\DataTables;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Helper;
use App\Helpers\Constant;
use App\Helpers\EmailHandler;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            $data['USER_STATUS'] = Constant::USER_STATUS;
            return view('users.index',$data);
        }catch (\Exception $e){
            AppException::log($e);
            return redirect()->back()->withErrors($e->getMessage());
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['status'] = Constant::USER_STATUS;
        $data['user'] = [];
        return view('users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check if the incoming request is valid...
        $requestData = $request->all();
        $requestData['phone'] = isset($requestData['phone']) ?
            str_replace("-", "", $request->phone) : null;

        $validationRule = User::getValidationRules('createUser', $requestData);
        $validator = Validator::make($requestData, $validationRule);
        if ($validator->fails())
        {
            return ApiResponseHandler::validationError($validator->errors());
        }
        // Retrieve the validated input data...
        $data = $this->storeOrUpdate($requestData, Constant::CRUD_STATES['created']);
        return ApiResponseHandler::success($data);
    }

    private function storeOrUpdate($validated, $state, $id = false)
    {
        DB::beginTransaction();
        if ($state == Constant::CRUD_STATES['created']) {
            $user = new User();
            $user->password = Hash::make($validated['password']);
        } else {
            $user = User::findById($id);
            if(!empty($validated['password']) && $validated['password'] !== "password"){
                $user->password = Hash::make($validated['password']);
            }
        }
        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->country_code = $validated['country_code'];
            $user->phone_number = $validated['phone'];
            $user->user_type = Constant::USER_TYPES['Admin'];
//            $user->image = $image;
            $user->status = $validated['is_active'] == 1 ? Constant::USER_STATUS['Active'] : Constant::USER_STATUS['InActive'];
            if ((!$user->save())) //|| (!$mapped)
            {
                throw new \Exception("Oopss we are facing some hurdle right now to process this action, please try again");
            }
            DB::commit();
            $return['type'] = 'success';
            $action = array_flip(Constant::CRUD_STATES);
            $return['message'] = 'User has been ' . $action[$state] . ' successfully.';
            return $return;
        } catch (\Exception $e) {
            AppException::log($e);
            DB::rollback();
            $return['type'] = 'errors';
            $get_environment = env('APP_ENV', 'local');
//            if ($get_environment == 'local') {
                $return['message'] = $e->getMessage();
//            } else {
//                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
//            }
            return $return;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['user'] = User::findById($id);
        $data['status'] = Constant::USER_STATUS;
        return view('admin.modules.users.edit.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findById($id);
        if(empty($user))
        {
            return redirect('/users')->with('warning_msg', "Record not found.");
        }else{
            $data['user'] = $user;
            $data['status'] = Constant::USER_STATUS;
            return view('users.edit', $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        // Check if the incoming request is valid...
        $requestData = $request->all();
        $requestData['phone'] = isset($requestData['phone']) ?
            str_replace("-", "", $request->phone) : null;

        $validationRule = User::getValidationRules('updateUser', $requestData);
        $validator = Validator::make($requestData, $validationRule);
        if ($validator->fails())
        {
            return ApiResponseHandler::validationError($validator->errors());
        }
        // Retrieve the validated input data...
        $data = $this->storeOrUpdate($requestData, Constant::CRUD_STATES['updated'], $id);
        return ApiResponseHandler::success($data);
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
            $usersRecord = User::getUsersByFilters($filter);
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
                $class = '';
                $disabled = '';
                if (!empty($rowdata->deleted_at))
                {
                    $disabled = 'disabled="disabled"';
                }
                return '<input type="checkbox" ' . $disabled . ' name="data_raw_id[]"  class="theClass ' . $class . '" value="' . $rowdata->id . '">';
            })
            ->addColumn('name', function ($rowdata) {
                $disabledClass = "";
                $url = url("/users/" . $rowdata->id.'/edit');
                $target = "_blank";
                return '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->name . '</a>';
            })
            ->addColumn('phone', function ($rowdata) {
                return "+(".$rowdata->country_code.")".$rowdata->phone_number;
            })
            ->addColumn('user_type', function ($rowdata) {
                $isActive = $rowdata->user_type;
                $userStatus = array_flip(Constant::USER_TYPES);
                return '<label class="badge badge-' . Constant::USER_TYPES_STYLE[$isActive] . '"> ' . $userStatus[$isActive] . '</label>';
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = $rowdata->status;
                $isOppositeStatus = $rowdata->status == Constant::USER_STATUS['InActive'] ? Constant::USER_STATUS['Active'] : Constant::USER_STATUS['InActive'];
                $userStatus = array_flip(Constant::USER_STATUS);
                return '<label
                    class="badge badge-' . Constant::USER_STATUS_STYLE[$isActive] . '"
                    onClick="App.Users.changeStatus(' . $rowdata->id . ',' . $isOppositeStatus . ')"
                > ' . $userStatus[$isActive] . '</label>';
            })
            ->addColumn('last_login', function ($rowdata) {
                if(empty($rowdata->last_login))
                    return null;
                return Helper::dated_by(null,$rowdata->last_login);
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

    /**
     * Remove all the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        try
        {
            $requestData = $request->all();
            $validationErrors = Helper::validationErrors($request, [
                'id' => 'required',
            ]);
            if ($validationErrors)
            {
                return ApiResponseHandler::validationError($validationErrors);
            }
            User::deleteAccount($requestData);
            $return['type'] = 'success';
            $return['message'] = __('messages.products.deleted');
            return ApiResponseHandler::success($return);
        } catch (\Exception $e) {
            $return['type'] = 'errors';
            $get_environment = env('APP_ENV', 'local');
            if ($get_environment == 'local') {
                $return['message'] = $e->getMessage();
            } else {
                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
            }
            return ApiResponseHandler::failure($return);
        }
    }

    public function changeUserStatus(Request $request)
    {
        $return = [];
        try {
            $requestData = $request->all();
            User::changeRecordStatus($requestData);
            $return['type'] = 'success';
            $return['message'] = "Selected user status changed successfully.";
            return ApiResponseHandler::success($return);
        } catch (\Exception $e) {
            $return['type'] = 'errors';
            $get_environment = env('APP_ENV', 'local');
            if ($get_environment == 'local') {
                $return['message'] = $e->getMessage();
            } else {
                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
            }
            return ApiResponseHandler::failure($return);
        }
    }

    public function deleteSelectedUsers(Request $request)
    {
        $return = [];
        try {
            $requestData = $request->all();
            User::deleteRecords($requestData);
            $return['type'] = 'success';
            $return['message'] = "Selected users deleted successfully.";
            return ApiResponseHandler::success($return);
        } catch (\Exception $e) {
            AppException::log($e);
            $return['type'] = 'errors';
            $get_environment = env('APP_ENV', 'local');
            if ($get_environment == 'local') {
             $return['message'] = $e->getMessage();
            } else {
                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
            }
            return ApiResponseHandler::failure($return);
        }
    }
}
