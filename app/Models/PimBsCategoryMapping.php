<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class PimBsCategoryMapping extends Model
{
    public $table = 'pim_bs_category_mapping';

    public static function getAllMerchantCategoryIds( $bSecureCategoryIds )
    {
        return self::whereIn('bs_category_id', $bSecureCategoryIds)->pluck('pim_category_id');
    }
}
