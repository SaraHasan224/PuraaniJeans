<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class PimAttributeOption extends Model
{
    protected $guarded = [];
    protected $table = 'pim_attribute_options';

    public static function getById($attrId){
        return self::whereId($attrId)->first();
    }

    public static function saveOption($data)
    {
        return self::updateOrCreate($data);
    }

    public static function getOptionByValue($attrId, $value)
    {
        return self::where('attribute_id', $attrId)->where('option_value', $value)->first();
    }
}
