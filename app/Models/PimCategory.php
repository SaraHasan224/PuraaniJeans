<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PimCategory extends Model
{
    protected $guarded = [];

    public function getNameAttribute($value)
    {
        $localizedName = $this->attributes[Helper::getLocalizedColumn('name')];
        if (empty($localizedName))
        {
            return $value;
        }

        return $localizedName;
    }

    public function getBannerAttribute($value)
    {
        if (empty($value))
        {
            return Helper::getProductImagePlaceholder(true);
        }
        $image = env('ENV_FOLDER') . $value;
        return Helper::getImgixImage($image, false);
    }

    public function getImageAttribute($value)
    {
        if (empty($value))
        {
            return Helper::getProductImagePlaceholder(true);
        }
        $image = env('ENV_FOLDER') . $value;
        return Helper::getImgixImage($image, false,200);
    }

    public static function getStoreCategoryBySlug($catSlug, $storeId)
    {
        return self::where('handle', $catSlug)->where('store_id', $storeId)->where('is_active', Constant::Yes)->first();
    }

    public static function saveCategorywithMapping($productId, $productData)
    {
        $categories = $productData['categories'];
        if(isset($productData['shopify_single_category']) && !empty( $productData['shopify_single_category'] ))
        {
            $categories = [$productData['shopify_single_category']];
        }

        $categoryIds = [];
        foreach($categories as $category)
        {
            $categoryId = self::updateOrCreate(
              [
                'store_id' => $productData['store_id'],
                'name' => $category['title']
              ],
              [
                'name_ur' => $category['title'],
                'is_imported' => Constant::Yes,
                'handle' => Str::slug($category['title']),
                'batch_id' => $productData['batch_id']
              ]
            )->id;

            PimProductCategory::mapProductCategory($productId, $categoryId);
            $categoryIds[] = $categoryId;
        }

        if(empty($productData['shopify_single_category']))
        {
            PimProductCategory::deleteMappings( $productId, $categoryIds );
        }
    }
}
