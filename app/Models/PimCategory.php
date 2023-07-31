<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PimCategory extends Model
{
    protected $guarded = [];

    public static function addParentPimCategory($closet, $name){
        $category = self::updateOrCreate([
            'closet_id' => $closet->id,
            'parent_id' => Constant::No,
            'position' => Constant::Yes,
            'name' => $name,
        ]);

        return $category;
    }

    public static function addChildPimCategory($closet, $parent, $name){
        $category = self::updateOrCreate([
            'closet_id' => $closet->id,
            'parent_id' => $parent->id,
            'position' => Constant::Yes,
            'name' => $name,
        ]);
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
