<?php

namespace App\Http\Controllers\Api\Products;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class RecentlyViewedProductController extends Controller
{
    /**
     * @OA\Post(
     *
     *     path="/api/recently-viewed/products",
     *     tags={"Products"},
     *     summary="Get customer's recently viewed products",
     *     operationId="getRecentlyViewedProducts",
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
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getRecentlyViewedProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            $customerId = $requestData['customer_id'];
            $productId = array_key_exists('product_id', $requestData) ? $requestData['product_id'] : "";
            $response['products'] = $this->getCachedRecentlyViewedProducts($customerId, $productId);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    public function getCachedRecentlyViewedProducts($customerId, $productId)
    {
        $listType = Constant::PJ_PRODUCT_LIST['RECENTLY_VIEWED_PRODUCTS'];
        $listOptions = [
            'limit_record' => 5,
            'customer_id' => $customerId,
            'exclude_product' => $productId
        ];
        return PimProduct::getProductsForApp($listType, 5, $listOptions, true);
    }
}
