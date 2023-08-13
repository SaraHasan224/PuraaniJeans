<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class PimBrand extends Model
{
    protected $guarded = [];

    public function getIconAttribute($value)
    {
        return asset("storage/".$value);
    }

    public static function getAllBrands()
    {
        return self::select('id', 'name', 'icon')->where('status',Constant::Yes)->get()->toArray();
    }

    public static function getAllBrandCategories()
    {
        return self::select('id as value', 'name as label', 'icon')->where('status',Constant::Yes)->get()->toArray();
    }

    public static function saveBrand($name)
    {
        return self::firstOrCreate(['name' => $name]);
    }

    public static function checkExistingBrand($name)
    {
        return self::select('id')->where('name',$name)->where('is_active', Constant::Yes)->first();
    }
}
