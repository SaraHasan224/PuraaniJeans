<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProductRecentlyViewed extends Model
{
//    use SoftDeletes;

    protected $table = "customer_product_recently_viewed";
    protected $guarded = [];

    public static function viewProduct($requestData)
    {
        return self::updateOrCreate([
            'customer_id'     => $requestData['customer_id'],
            'product_id'      => $requestData['product_id'],
        ], [
            'referrer_type'   => array_key_exists('referrer_type', $requestData) ? $requestData['referrer_type'] : Constant::PJ_PRODUCT_LIST['FEATURED_PRODUCTS'],
            'viewed_at'       => Carbon::now()
        ]);
    }

    public static function findByCustomerId($customerId)
    {
        return self::where('customer_id',$customerId)->get();
    }
    public static function findCountByClosetProductIds($productIds)
    {
        return self::whereIn('product_id',$productIds)->count();
    }


    public static function getRecentlyViewedResultsForCustomer($customerId)
    {
        return self::where('customer_id',$customerId)->orderBy('viewed_at','ASC')->latest()->take(5)->pluck('product_id')->toArray();
    }

    public static function updateRecentlyViewedCustomerId( $oldCustomerId, $newCustomerId )
    {
        $productIds = self::findByCustomerId($newCustomerId)->pluck('product_id')->toArray();
        self::where('customer_id', $oldCustomerId)->whereIn('product_id', $productIds)->delete();
        self::where('customer_id', $oldCustomerId)->update(['customer_id' => $newCustomerId]);
    }
}