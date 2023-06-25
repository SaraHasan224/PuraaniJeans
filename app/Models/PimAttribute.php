<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class PimAttribute extends Model
{
    protected $guarded = [];
    protected $table = 'pim_attributes';

    public function options()
    {
        return $this->hasMany(PimAttributeOption::class, 'attribute_id', 'id');
    }


    public static function getById($attrId){
        return self::whereId($attrId)->first();
    }

    public static function getPimAttributes($storeId){
        $fields =[
            'id',
            'name'
        ];
        $result = self::where('store_id',$storeId)->select($fields)->get();
        return $result;
    }

    public static function saveAttribute($attribute)
    {
        $attribute =  self::updateOrCreate([
            'name' => $attribute,
            'status' => Constant::Yes
        ]);
        return $attribute;
    }

    public static function getAttributeByNameAndStoreId($storeId, $name)
    {
        return self::where('store_id', $storeId)->where('name', $name)->first();
    }
}
