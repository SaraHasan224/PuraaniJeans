<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            $data['status'] = Constant::CUSTOMER_STATUS;
            $data['subStatus'] = Constant::CUSTOMER_SUBSCRIPTION_STATUS;
            return view('customers.index',$data);
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    private function storeOrUpdate($validated, $state, $image = "", $id = false)
    {
        DB::beginTransaction();
        if ($state == Constant::CRUD_STATES['created']) {
            $user = new Customer();
            $user->password = Hash::make($validated['password']);
        } else {
            $user = Customer::findById($id);
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
            $user->status = $validated['is_active'] == 1 ? Constant::CUSTOMER_STATUS['Active'] : Constant::CUSTOMER_STATUS['InActive'];
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
            if ($get_environment == 'local') {
                $return['message'] = $e->getMessage();
            } else {
                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
            }
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
        $data['customer'] = Customer::findById($id);
        $data['status'] = Constant::CUSTOMER_STATUS;
        $data['subStatus'] = Constant::CUSTOMER_SUBSCRIPTION_STATUS;
        return view('customers.edit.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Customer::findById($id);
        if(empty($user))
        {
            return redirect('/customers')->with('warning_msg', "Record not found.");
        }else{
            $data['customer'] = $user;
            $data['status'] = Constant::CUSTOMER_STATUS;
            $data['subStatus'] = Constant::CUSTOMER_SUBSCRIPTION_STATUS;
            return view('customers.edit', $data);
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

        $validationRule = Customer::getValidationRules('updateUser', $requestData);
        $validator = Validator::make($requestData, $validationRule);
        if ($validator->fails())
        {
            return ApiResponseHandler::validationError($validator->errors());
        }
//        $image = $user->image;
//        if ($request->hasFile('image')) {
//            $image_tmp = $request->image;
//            if ($image_tmp->isValid()) {
//                $extension = $image_tmp->getClientOriginalExtension();
//                $image = strtolower(trim($request->name)) . '_' . strtotime(Carbon::now()) . "." . $extension;
//                $image_path = public_path('assets/images/uploads/users/' . $image);
//                Image::make($image_tmp)->save($image_path);
//            }
//        }
        // Retrieve the validated input data...
        $data = $this->storeOrUpdate($requestData, Constant::CRUD_STATES['updated'], "", $id);
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
            $usersRecord = Customer::getByFilters($filter);
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
            ->addColumn('id', function ($rowdata) {
                $disabledClass = "";
                $url = url("/customers/" . $rowdata->id.'/edit');
                $target = "_blank";
                return '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->id . '</a>';
            })
            ->addColumn('name', function ($rowdata) {
                return $rowdata->first_name." ".$rowdata->last_name;
            })
            ->addColumn('email', function ($rowdata) {
                $email =  $rowdata->email;
                $badgeClass = !empty($rowdata->email_verified_at) ? "success" : "warning";
                $isVerified = !empty($rowdata->email_verified_at) ? "Verified" : "Verification Pending";
                $email .= '<br/><label class="badge badge-' . $badgeClass . '"> ' . $isVerified . '</label>';
                return $email;
            })
            ->addColumn('phone', function ($rowdata) {
                $phone =  "+(".$rowdata->country_code.")".$rowdata->phone_number;
                $badgeClass = !empty($rowdata->phone_verified_at) ? "success" : "warning";
                $isVerified = !empty($rowdata->phone_verified_at) ? "Verified" : "Verification Pending";
                $phone .= '<br/><label class="badge badge-' . $badgeClass . '"> ' . $isVerified . '</label>';
                return $phone;
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = !empty($rowdata->status) ? $rowdata->status : Constant::CUSTOMER_STATUS['InActive'];
                $userStatus = array_flip(Constant::CUSTOMER_STATUS);
                return '<label class="badge badge-' . Constant::CUSTOMER_STATUS_STYLE[$isActive] . '"> ' . $userStatus[$isActive] . '</label>';
            })
            ->addColumn('country', function ($rowdata) {
                return $rowdata->country_id;
            })
            ->addColumn('subscription_status', function ($rowdata) {
                $isSubcription = !empty($rowdata->subscription_status) ? $rowdata->subscription_status : Constant::CUSTOMER_SUBSCRIPTION_STATUS['disabled'];
                $userStatus = array_flip(Constant::CUSTOMER_SUBSCRIPTION_STATUS);
                return '<label class="badge badge-' . Constant::CUSTOMER_SUBSCRIPTION_STATUS_STYLE[$isSubcription] . '"> ' . $userStatus[$isSubcription] . '</label>';
            })
            ->addColumn('last_login', function ($rowdata) {
                if(empty($rowdata->last_login))
                    return null;
                return Helper::dated_by(null,$rowdata->last_login);
            })
            ->addColumn('created_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata->created_at);
            })
            ->addColumn('updated_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata->updated_at);
            })
            ->rawColumns([
                'check',
                'id',
                'name',
                'phone',
                'email',
                'status',
                'subscription_status',
                'last_login',
                'created_at','updated_at'])
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
    public function deleteRecords(Request $request)
    {
        try
        {
            $requestData = $request->all();
            $validationErrors = Helper::validationErrors($request, [
                'delete_ids' => 'required',
            ]);

            if ($validationErrors)
            {
                return ResponseHandler::validationError($validationErrors);
            }
            if ($requestData['action'] == 'delete')
            {
                Customer::deleteRecords($requestData);
            }
            else
            {
                Customer::updateRecords( $requestData);
            }
            return ResponseHandler::success([], __('messages.products.deleted'));
        }
        catch (\Exception $e)
        {
            return ResponseHandler::serverError($e);
        }
    }
}
