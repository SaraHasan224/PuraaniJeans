<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class PimBrand extends Model
{
    protected $guarded = [];

    public static function saveBrand($name)
    {
        return self::firstOrCreate(['name' => $name]);
    }

    public static function checkExistingBrand($name)
    {
        return self::select('id')->where('name',$name)->where('is_active', Constant::Yes)->first();
    }
}
