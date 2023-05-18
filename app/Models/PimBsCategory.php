<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PimBsCategory extends Model
{
    use SoftDeletes;
    public $table = 'pim_bs_category';

    public function getNameAttribute($value)
    {
        return $this->attributes[ Helper::getLocalizedColumn('name') ];
    }

    public function getIconAttribute($value)
    {
        $image = env('ENV_FOLDER') . $value;
        return Helper::getImgixImage($image, false,200);
    }

    public function children()
    {
        return $this->hasMany(PimBsCategory::class, 'parent_id', 'id')
            ->select('id', 'parent_id')
            ->where('is_active', Constant::Yes)
            ->with('children');
    }

    public function parent(){
        return $this->belongsTo(PimBsCategory::class, 'parent_id', 'id')->select('id','name', 'slug', 'parent_id')
          ->where('is_active', Constant::Yes);
    }

    public static function getCategoryBySlug( $slug )
    {
        return self::select('id', 'name', 'slug', 'parent_id')
            ->where('slug', $slug)
            ->where('is_active', Constant::Yes)
            ->with('parent')
            ->first();
    }

    public static function getFeaturedCategories()
    {
        return self::select('name', 'slug', 'icon', 'product_count')
            ->where('is_active', Constant::Yes)
            ->where('is_featured', Constant::Yes)
            ->where('product_count', '>' , 0)
            ->orderBy('position')
            ->get()
            ->take(4);
    }

    public static function getCategories( $parentId = 0 )
    {
        return self::select('name', 'slug', 'icon', 'product_count')
            ->where('parent_id', $parentId)
            ->where('is_active', Constant::Yes)
            ->where('product_count', '>' , 0)
            ->orderBy('position')
            ->get();
    }

    public static function getAllSubCategoryIds( $slug )
    {
        $allCategories = self::select('id')
            ->where('slug', $slug)
            ->where('is_active', Constant::Yes)
            ->with('children')
            ->first();

        if($allCategories)
        {
            $allCategories = $allCategories->toArray();
            array_walk_recursive($allCategories, 'self::getRecursiveIds');
            return self::$categoryIds;
        }
        
        return [];
    }

    public static $categoryIds;
    public static function getRecursiveIds($value, $key)
    {
        if($key == 'id')
        {
            self::$categoryIds[] = $value;
        }
    }
}
