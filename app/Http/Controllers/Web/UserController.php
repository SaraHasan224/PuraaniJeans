<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data['USER_STATUS'] = array_flip(Constant::USER_STATUS);
            return view('users.index',$data);
        }catch (\Exception $e){
            AppException::log($e);
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
        return view('admin.modules.users.add.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsersRequest $request)
    {
        // Check if the incoming request is valid...
        (object)$validated = $request->validated();
        $image = Auth::user()->image;
        if ($request->hasFile('image')) {
            $image_tmp = $request->image;
            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $image = strtolower(trim($request->name)) . '_' . strtotime(Carbon::now()) . "." . $extension;
                $image_path = public_path('assets/images/uploads/users/' . $image);
                Image::make($image_tmp)->save($image_path);
            }
        }
        // Retrieve the validated input data...
        $data = $this->storeOrUpdate($validated, $image, Constant::CRUD_STATES['create']);
        return redirect('admin/users')->with($data['type'], $data['message']);

    }

    private function storeOrUpdate($validated, $image, $state, $id = false)
    {
        DB::beginTransaction();
        if ($state == Constant::CRUD_STATES['create']) {
            $user = new User();
            $user->password = Hash::make($validated['password']);
        } else {
            $user = User::getRecordById($id);
            if(!empty($validated['password']) && $validated['password'] !== "password"){
                $user->password = Hash::make($validated['password']);
            }
        }
        try {
            $user->name = $validated['name'];
            $user->role = $validated['role'];
            $user->email = $validated['email'];
            $user->image = $image;
            $user->status = $validated['status'];
            if ((!$user->save())) //|| (!$mapped)
            {
                throw new \Exception("Oopss we are facing some hurdle right now to process this action, please try again");
            } else {
                DB::commit();
            }

            $return['type'] = 'success';
            $return['message'] = 'User has been ' . $state . ' successfully.';
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
        $data['user'] = User::getRecordById($id);
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
        $user = User::getRecordById($id);
        if(empty($user))
        {
            return redirect('admin/users')->with('warning_msg', "Record not found.");
        }else{
            $data['user'] = $user;
            $data['status'] = Constant::USER_STATUS;
            return view('admin.modules.users.edit.index', $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsersRequest $request, $id)
    {
        // Check if the incoming request is valid...
        (object)$validated = $request->validated();
        $user = User::getRecordById($id);
        $image = $user->image;
        if ($request->hasFile('image')) {
            $image_tmp = $request->image;
            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $image = strtolower(trim($request->name)) . '_' . strtotime(Carbon::now()) . "." . $extension;
                $image_path = public_path('assets/images/uploads/users/' . $image);
                Image::make($image_tmp)->save($image_path);
            }
        }
        // Retrieve the validated input data...
        $data = $this->storeOrUpdate($validated, $image, Constant::CRUD_STATES['update'], $id);
        return redirect('admin/users')->with($data['type'], $data['message']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    // The incoming request is valid...
    //if($id == false)
    //{
    //    //CREATE NEW USER
    //$user = new User();
    //$user->created_by = Auth::user()->id;
    //$user->status = $validated['status'];
    //$user->password = bcrypt($validated['password']);
    //$return['message'] = 'User has been added successfully.';
    //}else{
    //    //SELECT USER'S RECORD
    //    $user =  User::whereId($id)->first();
//        if(!empty($validated['password'])){
//            $user->password = Hash::make($validated['password']);
//        }
    //    $user->status = $validated['status'];
    //    $return['message'] = 'User has been updated successfully.';
    //}
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
            dd($e);
        }
    }

    private function makeDatatable($data)
    {
        return \DataTables::of($data['records'])
            ->addColumn('check', function ($rowdata) {
                $class = '';
                $disbaled = '';
                if ($rowdata->is_super_admin == Constant::Yes || !empty($rowdata->deleted_at))
                {
                    $disbaled = 'disabled="disabled"';
                }
                return '<input type="checkbox" ' . $disbaled . ' name="data_raw_id[]"  class="theClass ' . $class . '" value="' . $rowdata->id . '">';

            })
            ->addColumn('name', function ($rowdata) {
                $disabledClass = $rowdata->is_super_admin == Constant::Yes ? "disable" : "";
                $url = $rowdata->is_super_admin == Constant::Yes ? "#" : url("admin/users/" . $rowdata->id.'/edit');
                $target = $rowdata->is_super_admin == Constant::Yes ? "" : "_blank";

                $return = '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->name . '</a>';
                if (!empty($rowdata->deleted_at)) {
                    $return .= '<br/><label class="badge badge-danger"> Deleted</label>';
                }
                return $return;
            })
            ->addColumn('email', function ($rowdata) {
                return $rowdata->email;
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = $rowdata->status ?? 1;
                $result = "";
                $result .= '<label class="badge badge-' . Constant::USER_STATUS_STYLE[$isActive] . '"> ' . Constant::USER_STATUS[$isActive] . '</label>';
                return $result;
            })
            ->addColumn('created_at', function ($rowdata) {
//                optional($rowdata->created_record)->name
                return Helper::dated_by(optional($rowdata->created_record)->name,$rowdata->created_at);
            })
            ->addColumn('updated_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata->updated_at);
            })
            ->rawColumns(['check', 'name', 'status','created_at','updated_at'])
//            ->setOffset($data['offset'])
//            ->setTotalRecords($data['count'])
            ->make(true);
    }


    /**
     * Remove all the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
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
                User::deleteRecords($requestData);
            }
            else
            {
                User::updateRecords( $requestData);
            }
            return ResponseHandler::success([], __('messages.products.deleted'));
        }
        catch (\Exception $e)
        {
            return ResponseHandler::serverError($e);
        }
    }

}
