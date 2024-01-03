<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Helpers\Constant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PimBsCategoryMapping extends Model
{
    public $table = 'pim_bs_category_mapping';

    protected $fillable = [
        'bs_category_id',
        'pim_category_id' ,
        'mapped_by',
        "mapped_at"
    ];


    public static function mapPimCategory($merchantCategory, $bsCategory){
        $findCategory = self::where('bs_category_id', $bsCategory->id)->where('pim_category_id', $merchantCategory->id)->first();
        if(!$findCategory) {
            $category = self::create([
                'bs_category_id' => $bsCategory->id,
                'pim_category_id' => $merchantCategory->id,
                'mapped_by' => 0,
                'mapped_at' => Carbon::now(),
            ]);
            return $category;
        }
        return $findCategory;
    }

    public static function getAllMappedCategoryIds( $bSecureCategoryIds )
    {
        return self::whereIn('bs_category_id', $bSecureCategoryIds)->pluck('pim_category_id');
    }
}
