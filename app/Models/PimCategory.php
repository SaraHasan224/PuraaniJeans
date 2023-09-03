<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function Ramsey\Uuid\v4;

class PimCategory extends Model
{
    protected $guarded = [];

    public function parentCategory()
    {
        return $this->belongsTo(PimCategory::class, 'parent_id', 'id');
    }

    public function parentBSCategory()
    {
        return $this->hasOne(PimBsCategory::class, 'slug', 'pim_cat_reference');
    }

    public static function addParentPimCategory($closet, $name){
        $bsCategory = PimBsCategory::getCategoryBySlug($name);

        $category = self::updateOrCreate([
            'closet_id' => $closet->id,
            'parent_id' => Constant::No,
        ], [
            'position' => Constant::Yes,
            'pim_cat_reference' => $bsCategory->slug,
            'name' => $bsCategory->name,
        ]);

        //Link Closet category to PimBsCategoryMapping
        PimBsCategoryMapping::mapPimCategory($category, $bsCategory);

        return $category;
    }

    public static function addChildPimCategory($closet, $parent, $name){
        $bsCategory = PimBsCategory::getCategoryBySlug($name);

        $category = self::updateOrCreate([
            'closet_id' => $closet->id,
        ], [
            'position' => Constant::Yes,
            'parent_id' => $parent->id,
            'pim_cat_reference' => $bsCategory->slug,
            'name' => $bsCategory->name,
        ]);
        //Link Closet category to PimBsCategoryMapping
        PimBsCategoryMapping::mapPimCategory($category, $bsCategory);
        return $category;
    }


    public static function getClosetCategory($closetId)
    {
        return self::select('name', 'pim_cat_reference', 'image')->where('closet_id', $closetId)->orderBy('position', "ASC")->get()->toArray();
    }


    public static function getClosetCategoryByCategoryRef($catSlug,$closetId)
    {
        return self::where('closet_id', $closetId)->where('pim_cat_reference', $catSlug)->first();
    }
}
