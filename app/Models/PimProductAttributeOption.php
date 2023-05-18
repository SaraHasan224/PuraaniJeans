<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class PimProductAttributeOption extends Model
{
    protected $guarded = [];
    protected $table = 'pim_product_attribute_options';

    public static function saveProductAttributeOption($data)
    {
        return self::updateOrCreate([
          'product_id'     => $data['product_id'],
          'attribute_id'   => $data['attribute_id'],
          'option_id'      => $data['option_id']
        ],
          [
            'pim_product_attribute_id'  => $data['pim_product_attribute_id'],
            'option_value'              => $data['option_value'] ?? ''
          ])->id;
    }

    public static function removeProductAttributeOptions($productId, $attributeId, $optionIds)
    {
        return self::where('product_id', $productId)
          ->where('attribute_id', $attributeId)
          ->whereNotIn('id', $optionIds)
          ->delete();
    }


}
