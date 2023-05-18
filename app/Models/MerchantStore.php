<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantStore extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function getNameAttribute($value)
    {
        return $this->attributes[Helper::getLocalizedColumn('name')];
    }

    public static $validationRules = [
        'store' => [
            'store_slug' => 'required',
        ],
        'storeCategories' => [
            'store_slug' => 'required|string|exists:merchant_stores,store_slug',
            'category_slug' => 'required|exists:pim_categories,handle'
        ],
    ];

    public function attribute()
    {
        return $this->hasOne(PimAttribute::class, 'store_id', 'id');
        //->where('is_active', Constant::Yes);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }



    /*
     * BRANDING RELATIONSHIP
    */
    public function branding()
    {
        return $this->hasOne(MerchantBranding::class, 'store_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(PimProduct::class, 'store_id', 'id')->where('is_active', Constant::Yes)->whereHas("activeVariants");
    }

    public static function getMerchantDefaultStore($merchantId)
    {
        return self::where('merchant_id', $merchantId)->where('is_default', Constant::Yes)->first();
    }

    public static function getStoreById($merchantId, $id)
    {
        return self::where('merchant_id', $merchantId)->whereId($id)->first();
    }

    public static function filterStoreIdsByStoreSlugs($storeSlugs)
    {
        return self::whereIn('store_slug',$storeSlugs)->pluck('id')->toArray();
    }

    public static function findById($id)
    {
        return self::whereId($id)->first();
    }

    public static function getCustomerPortalStore()
    {
        return self::where('store_slug', env('UNIVERSAL_CHECKOUT_STORE_SLUG'))->first();
    }

    public static function getStoreByStoreSlug($slug)
    {
        return self::whereRaw("LOWER(store_slug) LIKE '%" . (strtolower($slug)) . "%' ")->first();
    }


    public static function getByStoreSlug($slug)
    {
        return self::where("store_slug", $slug)->first();
    }

    public function isPimStore()
    {
        return $this->is_pim_enabled == Constant::Yes;
    }

    public static function getStoreBySlug($merchantId, $slug)
    {
        $store = self::whereRaw(" LOWER(store_slug) LIKE '%" . (strtolower($slug)) . "%' ");
        if (!empty($merchantId)) {
            $store = $store->where('merchant_id', $merchantId);
        }

        return $store->first();
    }

    public static function getMerchantStoreIds($merchantId)
    {
        return self::where('merchant_id', $merchantId)->get()->pluck('store_slug')->toArray();
    }

    public static function getStoreByAppId($appId)
    {
        return self::select('id', 'old_app_id', 'old_env_id')->where('old_app_id', $appId)->first();
    }

    public static function getMerchantStore($requestData)
    {
        $store = "";
        $merchant = Client::getClientById($requestData['client_id']);
        if ($merchant) {
            $merchant_id = $merchant->merchant_id;
            if (isset($requestData['merchant_store_id']) && !empty($requestData['merchant_store_id'])) {
                $store = self::getStoreBySlug($merchant_id, $requestData['merchant_store_id']);
            } else if (isset($requestData['store_id']) && !empty($requestData['store_id'])) {
                $store = self::getStoreBySlug($merchant_id, $requestData['store_id']);
            } else {
                $store = self::getMerchantDefaultStore($merchant_id);
            }
        }

        return $store;
    }

    public static function __getMetaData($store)
    {
        $unavailable = Constant::No;
        $errorMsgs   = '';

        $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();

        if(empty($shipment)) {
            $unavailable = Constant::Yes;
            $errorMsgs   = 'Delivery not available in your region';
        }

        $metadata = [
            'store_name'                => $store->name,
            'mechant_name'              => "My closet",
            'store_slug'                => $store->store_slug,
            'shipment_unavailable'      => $unavailable,
            'shipment_unavailable_msg'  => $errorMsgs,
            'fav_icon_url'              => $store->getStoreAppFavicon(),
            'desktop_logo_url'          => $store->getStoreAppWebsiteLogo(),
        ];

        return $metadata;
    }

    public static function getStoresListing($type, $perPage = "", $options = [])
    {
        $storeIds = array_key_exists('storeIds', $options) ? $options['storeIds'] : "";

        $query = self::select('id', 'merchant_id', 'name', 'name_ur', 'position', 'store_slug', 'is_featured', 'is_bshop_enabled', 'plugin_installed');

        if ($type == Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Featured']) {
            $query->where('is_featured', Constant::Yes);
        } else if ($type == Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Filters']) {
            $query->whereIn('id', $storeIds);
        }

        $query->whereHas('merchant', function($query) {
            $query->select('id','is_active','user_id')->where('is_active',Constant::Yes);
        });

        if ($type == Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Featured']) {
            $query->orderBy('position', 'ASC');
        } else {
            $query->orderBy('name', 'ASC');
        }

        if ($type == Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Featured']) {
            $query->orderBy('position', 'ASC');
        } else {
            $query->orderBy('name', 'ASC');
        }

        if (empty($perPage)) {
            $storesList = $query->whereHas('products')->paginate(40);
            $storesTransformed = $storesList
                ->getCollection()
                ->map(function ($item) {
                    $item['favicon'] = $item->getStoreAppFavicon();
                    $item['website'] = $item->getStoreAppWebsiteLogo();
                    unset($item->branding);
                    return $item;
                })->toArray();
            return $storesTransformed;
        } else {
        $storesList = $query
                    ->whereHas('products')
                    ->paginate($perPage);

            $storesTransformed = $storesList
                ->getCollection()
                ->map(function ($item) {
                    $item['favicon'] = $item->getStoreAppFavicon();
                    $item['website'] = $item->getStoreAppWebsiteLogo();
                    unset($item->branding);
                    return $item;
                })->toArray();

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $storesTransformed,
                $storesList->total(),
                $storesList->perPage(),
                $storesList->currentPage(), [
                    'path' => \Request::url(),
                    'query' => [
                        'page' => $storesList->currentPage()
                    ]
                ]
            );
        }
    }


    public function setShipmentAreaCharges($merchantShipmentMethod, $merchantShipmentMethodId, $address)
    {
        $areaBasedShipmentChargesAssigned = Constant::No;
        if ($merchantShipmentMethod->area_based_shipment_charges) {
            if (empty($address)) {
                return false;
            } else {
                $addressDetail = $address;
                $chargesData = MerchantShipmentAreaCharges::getMerchantShipmentAreaChargesDetails($merchantShipmentMethodId, $addressDetail);
                if (!empty($chargesData)) {
                    $this->shipment_amount = $chargesData;
                    $areaBasedShipmentChargesAssigned = Constant::Yes;
                } else {
                    $this->shipment_amount = $merchantShipmentMethod->charges;
                }
            }
        } else {
            $this->shipment_amount = $merchantShipmentMethod->charges;
        }
        return $this->shipment_amount ?? 0;
    }


    public function isProductAvailable($address)
    {
        $allow = Constant::No;
        if ($this->isAreaBasedShipment() == Constant::No) {
            $allow = Constant::Yes;
        }
        return $allow;

    }


    public function getStoreAppWebsiteLogo($width = 200)
    {
        $value = $this->branding->getOriginal('website');
        if (empty($value)) {
            $bsecure_logo = env('TAP_TAP_DEFAULT_LOGO_PATH');
            return Helper::getImgixImage($bsecure_logo);
        }
        return Helper::getImgixImage($value, false, $width);
    }

    public function getStoreAppFavicon()
    {
        $value = $this->branding->getOriginal('favicon');
        if (empty($value)) {
            $bsecure_logo = env('TAP_TAP_DEFAULT_FAVICON_PATH');
            return Helper::getImgixImage($bsecure_logo);
        }
        return Helper::getImgixImage($value, false, 200);
    }

    public function getStoreAppPostOrderSuccessMsg(){
        return $this->branding->post_order_msg;
    }

    public static function getStoresForPIM($isManual = true)
    {
        $stores = self::select('id', 'merchant_id', 'name', 'name_ur', 'store_url', 'integration_type')
          ->where('is_pim_enabled', Constant::Yes);
        if($isManual)
        {
            $stores = $stores->where('run_pim_cron', Constant::Yes);
        }

        return $stores->get();
    }
}
