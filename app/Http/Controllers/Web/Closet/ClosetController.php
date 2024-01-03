<?php

namespace App\Http\Controllers\Web\Closet;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Closet;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimProduct;
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
     * @OA\Post(
     *
     *     path="/api/product/{productId}",
     *     tags={"Products"},
     *     summary="Product detail",
     *     operationId="getProductDetail",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Get product detail",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="referrer_type",
     *                     example="Featured Product",
     *                     description="referrer type",
     *                     type="string"
     *                 )
     *              )
     *         )
     *     ),
     * )
     */

    public function getProductDetail(Request $request, $id) {
        try {
            $result = [];
            $requestData = $request->all();

            $products = PimProduct::getById($id);
            if (!empty($products)) {
                $requestData['product_id'] = $products->id;
                $result = PimProduct::getProductDetail($products->handle);
            }
            $productStatus = array_flip(\App\Helpers\Constant::PIM_PRODUCT_STATUS);
            return view('closet.closet.tabs.products_show', [
                'product' => $result,
                'productStyleStatus' => Constant::PIM_PRODUCT_STATUS_STYLE[$products->status],
                'productStatus' => $productStatus[$products->status]
            ]);
        } catch (\Exception $e) {
            AppException::log($e);
            $return['type'] = 'errors';
            $get_environment = env('APP_ENV', 'local');
            if ($get_environment == 'local') {
                $return['message'] = $e->getMessage();
            } else {
                $return['message'] = "Oopss we are facing some hurdle right now to process this action, please try again";
            }
            return ApiResponseHandler::failure($return, $return['message']);
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
                $url = url("/closets/" . $rowdata->closet_reference.'/edit');
                $target = "_blank";
                return '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->closet_name . '</a>';
            })
            ->addColumn('trending', function ($rowdata) {
                $disabledClass = "";
                $isActive = $rowdata->is_trending;
                $status = array_flip(Constant::CLOSET_TRENDING_STATUS);
                $oppStatus = (int) !boolval($isActive);
                $function = "App.Closet.closetTrendingModal('".$rowdata->closet_reference."','".$status[$oppStatus]."','".$oppStatus."')";
                $text = '<label
                    class="badge badge-' . Constant::CLOSET_TRENDING_STATUS_STYLE[$isActive] . '"
                    onclick="'.$function.'"
                    > ' . $status[$isActive] . '</label>';
                if($rowdata->is_trending) {
                    $text .= "<br/> <b>Trending Position: </b>".   $rowdata->trending_position;
                }
                return $text;
            })
            ->addColumn('customer_name', function ($rowdata) {
                $disabledClass = !empty($rowdata->customer->deleted_at);
                $url = url("/customers/" . $rowdata->customer->id.'/edit');
                $target = "_blank";
                return '<a target="'.$target.'" href="'.$url.'" class="'.$disabledClass.'" >' . $rowdata->customer->first_name .' '. $rowdata->customer->last_name.'</a>'.'<br/><b>Email:</b> '.$rowdata->customer->email;
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = $rowdata->status;
                $userStatus = array_flip(Constant::USER_STATUS);
                return '<label class="badge badge-' . Constant::USER_STATUS_STYLE[$isActive] . '"> ' . $userStatus[$isActive] . '</label>';
            })
            ->addColumn('logo', function ($rowdata) {
                return '<img src="'.$rowdata->logo.'" width="70" height="60"/>';
            })
            ->addColumn('banner', function ($rowdata) {
                return '<img src="'.$rowdata->banner.'"  width="100" height="100"/>';
            })
            ->addColumn('created_at', function ($rowdata) {
//                optional($rowdata->created_record)->name
                return Helper::dated_by(null,$rowdata->created_at);
            })
            ->addColumn('updated_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata->updated_at);
            })
            ->rawColumns(['check', 'closet_name', 'logo','banner', 'trending', 'customer_name', 'status','created_at','updated_at'])
            ->setOffset($data['offset'])
            ->with([
                "recordsTotal" => $data['count'],
                "recordsFiltered" => $data['count'],
            ])
            ->setTotalRecords($data['count'])
            ->make(true);
    }


    /**
     * Get list of the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getProductListingRecord(Request $request, $ref)
    {
        try {
            $filter = $request->all();
            $closet = Closet::findByReference($ref);
            $usersRecord = PimProduct::getByFilters($filter, $closet->id);
            $response = $this->makeProductListingDatatable($usersRecord);
            return $response;
        } catch (\Exception $e) {
            AppException::log($e);
            dd($e->getTraceAsString());
        }
    }

    private function makeProductListingDatatable($data)
    {
        return DataTables::of($data['records'])
            ->addColumn('check', function ($rowdata) {
                $disabled = '';
                return '<input type="checkbox" ' . $disabled . ' name="data_raw_id[]"  class="theClass" value="' . $rowdata['id'] . '">';
            })
            ->addColumn('name', function ($rowdata) {
                $id = $rowdata['id'];
                return '<button
                            type="button"
                            onclick="App.Closet.closetProductViewModal(this, '.$id.')"
                            title="'.$rowdata['name'].' Details"
                            submitTheme="btn-danger submitModelSuccess"
                            submitText="Close"
                            class="mr-2 border-0 btn-transition btn btn-outline-danger"
                            data-toggle="modal"
                            data-target="#customModalWrapper"
                                >
                                ' . $rowdata['name'] . '
                        </button>';
                return '<span>' . $rowdata['name'] . '</span>';
            })
            ->addColumn('bs_category', function ($rowdata) {
                $pimbSCategory = [];
                foreach ($rowdata['category'] as $_category) {
                    $pimbSCategory[] = optional(optional(optional($_category)->category)->parentBSCategory)->name;
                }
                return '<span>' . implode("| ", $pimbSCategory) . '</span>';
            })
            ->addColumn('category_name', function ($rowdata) {
                $pimCategory = [];
                foreach ($rowdata['category'] as $_category) {
                    $pimCategory[] =  optional(optional($_category->category)->parentCategory)->name. " / " . $_category->category->name;
                }
                return '<span>' . implode("| ", $pimCategory) . '</span>';
            })
            ->addColumn('status', function ($rowdata) {
                $isActive = $rowdata['status'];
                $status = array_flip(Constant::PIM_PRODUCT_STATUS);
                $oppStatus = (int) !boolval($isActive);
                $function = "App.Closet.closetProductChangeStatusModal('".$rowdata['handle']."','".$status[$oppStatus]."','".$oppStatus."')";
                return '<label class="badge badge-' . Constant::PIM_PRODUCT_STATUS_STYLE[$isActive] . '" onClick="'.$function.'"> ' . $status[$isActive] . '</label>';
            })
            ->addColumn('is_featured', function ($rowdata) {
                $isActive = (int) $rowdata['is_featured'];
                $status = array_flip(Constant::PIM_PRODUCT_FEATURED_STATUS);
                $oppStatus = (int) !boolval($isActive);
                $function = "App.Closet.closetProductMarkFeaturedModal('".$rowdata['handle']."','".$status[$oppStatus]."','".$oppStatus."')";
                $text = '<label class="badge badge-' . Constant::PIM_PRODUCT_FEATURED_STATUS_STYLE[$isActive] . '" onClick="'.$function.'"> ' . $status[$isActive] . '</label>';
                if(!empty($isActive)) {
                    $text .= '<br/><span> Position: ' . $rowdata['featured_position']. '</span>';
                }
                return $text;
            })
            ->addColumn('image', function ($rowdata) {
                $image = isset($rowdata['defaultImage'])? optional($rowdata['defaultImage'])->url : Helper::getProductImagePlaceholder();
                return '<img src="'.$image.'" width="70" height="120"/>';
            })
            ->addColumn('quantity', function ($rowdata) {
                return $rowdata['max_quantity'];
            })
            ->addColumn('discounted_price', function ($rowdata) {
                $defaultVariant = $rowdata['activeVariants']['0'];
                $discountedPrice = $defaultVariant->discounted_price;
                return $discountedPrice;
            })
            ->addColumn('discount', function ($rowdata) {
                $defaultVariant = $rowdata['activeVariants']['0'];
                $discount = $defaultVariant->discount;
                return empty($discount) ? 0 : $discount;
            })
            ->addColumn('shipping_price', function ($rowdata) {
                $enableWWShipping = $rowdata['enable_world_wide_shipping'] == 1;
                $badgeStyle = $enableWWShipping ? "success" : "danger";
                $badgeTitle = $enableWWShipping ? "Enabled" : "Disabled";
                $country = !empty($rowdata['shipment_country_details']) ? $rowdata['shipment_country_details']['name'] : "";

                $text = '<span> <b>Price:</b>' . $rowdata['shipping_price'] . '</span>';
                if(!empty($country)) {
                    $text .= '<br/><span> Country: ' . $country. '</span>';
                }
                $text .= '<br/><b>World Wide Shipping: </b><label class="badge badge-' . $badgeStyle . '"> ' . $badgeTitle . '</label>';

                return $text;
            })
            ->addColumn('created_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata['created_at']);
            })
            ->addColumn('updated_at', function ($rowdata) {
                return Helper::dated_by(null,$rowdata['updated_at']);
            })
            ->rawColumns(['check', 'name', 'bs_category','category_name', 'image','created_at','updated_at','status', 'is_featured','shipping_price'])
            ->setOffset($data['offset'])
            ->with([
                "recordsTotal" => $data['count'],
                "recordsFiltered" => $data['count'],
            ])
            ->setTotalRecords($data['count'])
            ->make(true);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $ref
     * @return \Illuminate\Http\Response
     */
    public function edit($ref)
    {
        $closet = Closet::findByReference($ref);
        if(empty($closet))
        {
            return redirect('/closet')->with('warning_msg', "Record not found.");
        }else{
            $closetProductIds = PimProduct::findProductIdsByCloset($closet->id);
            $data['closet'] = $closet;
            $data['dashboard'] = (object) [
                'order_count' => 0,
                'product_view_count' => CustomerProductRecentlyViewed::findCountByClosetProductIds($closetProductIds),
                'product_sold_count' => 0,
                'follower_count' => CustomerProductRecentlyViewed::findCountByClosetProductIds($closetProductIds),
            ];
            return view('closet.closet.edit', ['module' => "closet", 'data' => $data]);
        }
    }

    public function toggleTrendingStatus(Request $request)
    {
        try {
            $requestData = $request->all();
            $closetRef = $requestData['ref'];
            $status = $requestData['is_trending'];
            $position = array_key_exists('position', $requestData) ? $requestData['position'] : 0;

            Closet::updateTrendingStatus($closetRef, $status, $position);
            $return['type'] = 'success';
            $return['message'] = 'Trending status updated successfully.';
            return $return;
        } catch (\Exception $e) {
            AppException::log($e);
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

    public function toggleProductStatus(Request $request)
    {
        try {
            $requestData = $request->all();
            $handle = $requestData['handle'];
            $status = $requestData['status'];

            PimProduct::updateStatus($handle, $status);
            $return['type'] = 'success';
            $return['message'] = 'Product status has been updated successfully';
            return $return;
        } catch (\Exception $e) {
            AppException::log($e);
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


    public function toggleProductFeaturedStatus(Request $request)
    {
        try {
            $requestData = $request->all();
            $handle = $requestData['handle'];
            $status = $requestData['is_featured'];
            $position = array_key_exists('position', $requestData) ? $requestData['position'] : 0;

            PimProduct::updateFeaturedStatus($handle, $status, $position);
            $return['type'] = 'success';
            $return['message'] = 'Product feature status has been updated successfully';
            return $return;
        } catch (\Exception $e) {
            AppException::log($e);
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
}
