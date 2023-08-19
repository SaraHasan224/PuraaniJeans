<?php

namespace App\Http\Controllers\Api\Closet;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Http\Controllers\Controller;
use App\Models\Closet;
use App\Models\Customer;
use App\Models\MerchantStore;
use App\Models\PimAttribute;
use App\Models\PimAttributeOption;
use App\Models\PimBrand;
use App\Models\PimBsCategory;
use App\Models\PimCategory;
use App\Models\PimProduct;
use App\Models\PimProductAttribute;
use App\Models\PimProductAttributeOption;
use App\Models\PimProductCategory;
use App\Models\PimProductImage;
use App\Models\PimProductVariant;
use App\Models\PimProductVariantOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function Ramsey\Uuid\v4;
use function Symfony\Component\Console\Input\isArray;

class ClosetProductsController extends Controller
{

    /**
     * @OA\Get(
     *
     *     path="/api/meta-data/product",
     *     tags={"Products"},
     *     summary="Get Meta Data Products",
     *     operationId="getProductMeta",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getProductMeta()
    {
        try {
            $conditionAttributeId = PimAttribute::getAttributeByName('Condition')->id;
            $sizeAttributeId = PimAttribute::getAttributeByName('Size')->id;
            $standardAttributeId = PimAttribute::getAttributeByName('Standard')->id;
            $colorAttributeId = PimAttribute::getAttributeByName('Color')->id;
            $response = [
                'categories' => PimBsCategory::getAllProductCategories(),
                'brands' => PimBrand::getAllBrandCategories(),
                'condition' => $this->formatMetaOptions(PimAttributeOption::getByAttributeId($conditionAttributeId)),
                'size' => $this->formatMetaOptions(PimAttributeOption::getByAttributeId($sizeAttributeId)),
                'standard' => $this->formatMetaOptions(PimAttributeOption::getByAttributeId($standardAttributeId)),
                'color' => $this->formatMetaOptions(PimAttributeOption::getByAttributeId($colorAttributeId))
            ];
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    private function formatMetaOptions($array)
    {
        $arrayObj = [];
        foreach ($array as $key => $value) {
            $arrayObj[] = [
                'label' => $value->option_value,
                'value' => $value->id,
                'attribute_id' => $value->attribute_id
            ];
        }
        return $arrayObj;
    }


    /**
     * @OA\Post(
     *
     *     path="/api/add/product",
     *     tags={"Closet"},
     *     summary="Add New Products",
     *     operationId="addProduct",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Add New Products",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="filters",
     *                     description="Network Id of customer's phone number",
     *                     type="object",
     *                     example=Null
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function addProduct(Request $request)
    {
        try {
            $requestData = $request->all();
            DB::beginTransaction();

            $validator = Validator::make($requestData, PimProduct::getValidationRules('add-product',$requestData));
            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $customerId = $requestData['customer_id'];
            $customer = Customer::findById($customerId);
            $customerCloset = $customer->closet;
            if(empty($customerCloset)) {
                return ApiResponseHandler::failure("You should create a store first.");
            }

            $featuredPosition = Constant::No;
            $recommendedPosition = Constant::No;
            $product = (object)$requestData;

//            if ($requestData['is_featured'] == Constant::Yes) {
//                $featuredPosition += $featuredPosition + 1;
//            }
//            if ($requestData['is_recommended'] == Constant::Yes) {
//                $recommendedPosition += $recommendedPosition + 1;
//            }
            #Add PIM Product
            $productSku = $requestData['sku'];
            $requestData['is_featured'] = Constant::No;
            $requestData['is_recommended'] = Constant::No;

            $pimProduct = PimProduct::create([
                'closet_id' => $customerCloset->id,
                'brand_id' => array_key_exists('brands', $requestData) && array_key_exists('value', $requestData['brands']) ? $requestData['brands']['value'] : '',
                'name' => $requestData['name'],
                'sku' => $productSku,
                'handle' => Helper::generateSlugReference($productSku),
                'short_description' => $requestData['short_description'],
                'pim_product_reference' => v4(),
                'has_variants' => !empty($requestData['variants']) && count($requestData['variants']) > 1,
                'price' => $requestData['price'],
//                'discounted_price' => $requestData['discounted_price'],
                'max_quantity' => $requestData['max_quantity'],
//                'position' => $key + 1,
                'status' => Constant::Yes,
                'is_featured' => $requestData['is_featured'],
                'featured_position' => $featuredPosition,
                'featured_at' => $requestData['is_featured'] == Constant::Yes ? Carbon::now() : null,
                'is_recommended' => $requestData['is_recommended'],
                'recommended_position' => $recommendedPosition,
                'recommended_at' => $requestData['is_recommended'] == Constant::Yes ? Carbon::now() : null,
            ]);
            #Add PIM Product Images
            $pimProductImages = [];

            foreach ($requestData['images'] as $key2 => $images) {
                $imgFile = $images;
                $imgFileName = Helper::clean(trim(strtolower($productSku)));
                $imgFilePath = "images/closets/" . $customerCloset->id . "/products/" ;
                $imgImage = Helper::uploadFileToApp($imgFile, $imgFileName, $imgFilePath);

                $image = PimProductImage::create([
                    'product_id' => $pimProduct->id,
                    'url' => $imgImage,
                    'position' => 0,
                    'is_default' => $key2 == Constant::No ? Constant::Yes : Constant::No,
                    'status' => Constant::Yes,
                ]);
                $pimProductImages[] = $image->id;
            }

            #Add PIM Product Categories
            $category = '';
            $productCategory = $requestData['category'];
            $parentCategory = PimCategory::addParentPimCategory($customerCloset, $productCategory['parent']);
            if (!empty($productCategory['child'])) {
                $category = PimCategory::addChildPimCategory($customerCloset, $parentCategory, $productCategory['child']);
            }

            PimProductCategory::addPimCategory($pimProduct, $parentCategory, $category);
            #Add PIM Product Variants
            foreach ($requestData['variants'] as $key3 => $variants) {
                $pimVariants = [];
                $variantAttribute = [];
                $variantTitles = [];
                $variantTitle = "";
                $variantSKUs = [];
                $variantSKU = $productSku;
                foreach ($variants['variation'] as $key4 => $attributes) {
                        $attr = $attributes['name'];
                        $options = implode(' ', (array)$attributes['value']);

                        $attribute = PimAttribute::saveAttribute($attr);
                        $productAttribute = PimProductAttribute::saveAttribute($customerCloset, $pimProduct, $attribute, $attr);

                        $attributeOptions = PimAttributeOption::saveOption($attribute, $options);
                        $productAttributeOptions = PimProductAttributeOption::saveProductAttributeOption($pimProduct, $attribute, $productAttribute, $attributeOptions, $options);

                        $variantAttribute[$key3][] = [
                            'attrId' => $productAttribute->id,
                            'optionId' => $productAttributeOptions->id,
                        ];
                        $variantTitle = $key4 == 0 ? $options : $variantTitle . " / " . $options;
                        $variantSKU = $variantSKU . "-" . substr($options, 0, 3);

                        $variantTitles[$key3] = (string)$variantTitle;
                        $variantSKUs[$key3] = (string)$variantSKU;
                }
                $pimVariants[$key3] = [
                    'attr' => $variantAttribute[$key3],
                    'variantTitles' => $variantTitles[$key3],
                    'variantSKU' => $variantSKUs[$key3],
                ];

                foreach ($pimVariants as $vKey => $variant) {
                    $pimProductVariants = PimProductVariant::create([
                        'product_id' => $pimProduct->id,
                        'product_variant' => $variant['variantTitles'],
                        'sku' => $variant['variantSKU'],
                        'quantity' => $variants['qty'],
                        'price' => $variants['price'],
                        'discount' => $variants['price']-$variants['discounted_price'],
                        'discount_type' => Constant::DISCOUNT_TYPE['flat'],
                        'image_id' => $pimProductImages[$key3 + 1],
                        'short_description' => $variants['description'],
                        'status' => Constant::Yes,
                    ]);
                    foreach ($variant['attr'] as $key5 => $attributes) {
                        foreach ($attributes as $attribute) {
                            try {
                                PimProductVariantOption::saveProductAttributeOption([
                                    'product_id' => $pimProduct->id,
                                    'variant_id' => $pimProductVariants->id,
                                    'attribute_id' => $attribute['attrId'],
                                    'option_id' => $attribute['optionId'],
                                ]);
                            } catch (\Exception $e) {
                                AppException::log($e);
                            }
                        }
                    }
                }
            }
            DB::commit();
            return ApiResponseHandler::success([], __('messages.general.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getTraceAsString());
        }
    }

    /**
     * @OA\Post(
     *
     *     path="/api/filter/closet/{slug}/product",
     *     tags={"Closet"},
     *     summary="Store Products",
     *     operationId="getFilteredClosetProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="store slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Resend OTP",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="filters",
     *                     description="Network Id of customer's phone number",
     *                     type="object",
     *                     example=Null
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public function getFilteredClosetProducts(Request $request, $closetRef)
    {
        try {
            $requestData = $request->all();
            $response = [];
            $requestData['store_slug'] = $closetRef;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($closetRef);
            $validator = Validator::make($requestData, PimProduct::getValidationRules('filters', $requestData));

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $filterData = $requestData['filters'];

            if ($store) {
                if (!($store->merchant->canPlaceOrder(env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))) {
                    return ApiResponseHandler::failure(__('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes]);
                }
                $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
                $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();
                $listOptions = [
                    'store' => $store,
                    'filters' => $filterData,
                    'store_disabled' => empty($shipment) ? Constant::Yes : Constant::No,
                    'shipment_error' => empty($shipment) ? 'Delivery not available in your region.' : ''
                ];
                $perPage = 12;
                $response = PimProduct::getProductsForApp($listType, $perPage, $listOptions);
                return ApiResponseHandler::success($response, __('messages.general.success'));
            }
            return ApiResponseHandler::failure("Store not found", '', ['not_found' => Constant::Yes]);
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/product",
     *     tags={"Closet"},
     *     summary="Store Products",
     *     operationId="getClosetProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="store slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public
    function getClosetProducts(Request $request, $closetRef)
    {
        try {
            $response = [];
            $requestData['store_slug'] = $closetRef;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $store = self::getCachedStore($closetRef);
            if ($store) {
                if (!($store->merchant->canPlaceOrder(env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))) {
                    return ApiResponseHandler::failure(__('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes]);
                }

                $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();
                $response = self::getCachedClosetProducts($request, $store, $shipment);
                return ApiResponseHandler::success($response, __('messages.general.success'));
            }
            return ApiResponseHandler::failure("Store not found", '', ['not_found' => Constant::Yes]);
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    public
    function getCachedStore($closetRef)
    {
        $cacheKey = 'get_store_' . $closetRef;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($closetRef) {
            return Closet::getByStoreSlug($closetRef);
        });
    }

    public
    function getCachedClosetProducts($request, $store, $shipment)
    {
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_' . $store->store_slug . '_all_products_' . $page . '_' . $perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $shipment) {
            $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
            $listOptions = [
                'store' => $store,
                'store_disabled' => empty($shipment) ? Constant::Yes : Constant::No,
                'shipment_error' => empty($shipment) ? 'Delivery not available in your region.' : ''
            ];

            return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
        });
    }


    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/category/{catSlug}/product",
     *     tags={"Closet"},
     *     summary="Manage Closet Category Products",
     *     operationId="getClosetCategoryProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="catSlug",
     *         in="path",
     *         description="category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public
    function getClosetCategoryProducts(Request $request, $closetRef, $catSlug)
    {
        try {
            $response = [];
            $requestData = [];
            $requestData['store_slug'] = $closetRef;
            $requestData['category_slug'] = $catSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['storeCategories']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($closetRef);
            if ($store) {
                if (!($store->merchant->canPlaceOrder(env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))) {
                    return ApiResponseHandler::failure(__('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes]);
                }
                $category = self::getCachedCategory($catSlug, $store->id);
                if (empty($category)) {
                    return ApiResponseHandler::failure(__('messages.app.stores.products.category.failure'), '', ['not_found' => Constant::Yes]);
                }
                $response['products'] = self::getCachedStoreCategoryProducts($request, $store, $category);
                return ApiResponseHandler::success($response, __('messages.app.stores.products.category.success'));
            }
            return ApiResponseHandler::failure("Store not found", '', ['not_found' => Constant::Yes]);
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.app.stores.products.category.failure'), $e->getMessage(), ['not_found' => Constant::Yes]);

        }
    }

    public
    function getCachedCategory($catSlug, $storeId)
    {
        $cacheKey = 'get_store_' . $storeId . '_category_' . $catSlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($catSlug, $storeId) {
            return PimCategory::getClosetCategoryByCategoryRef($catSlug, $storeId);
        });
    }

    public
    function getCachedStoreCategoryProducts($request, $store, $category)
    {
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_category_products_' . $store->store_slug . '_' . $page . '_' . $perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $category) {
            $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
            $listOptions = [
                'store' => $store,
                'categoryId' => $category->id,
            ];
            return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
        });
    }
}
