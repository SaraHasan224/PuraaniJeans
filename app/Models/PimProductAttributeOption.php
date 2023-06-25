<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class PimProductAttributeOption extends Model
{
    protected $guarded = [];
    protected $table = 'pim_product_attribute_options';

    public static function saveProductAttributeOption($pimProduct, $attribute, $productAttribute, $attributeOptions, $options)
    {
        return self::updateOrCreate([
          'product_id'     => $pimProduct->id,
          'attribute_id'   => $attribute->id,
          'option_id'      => $attributeOptions->id
        ],
          [
            'pim_product_attribute_id'  => $productAttribute->id,
            'option_value'              => $options
          ]);
    }

    public static function removeProductAttributeOptions($productId, $attributeId, $optionIds)
    {
        return self::where('product_id', $productId)
          ->where('attribute_id', $attributeId)
          ->whereNotIn('id', $optionIds)
          ->delete();
    }


}
