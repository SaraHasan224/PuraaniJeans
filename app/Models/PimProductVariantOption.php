<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class PimProductVariantOption extends Model
{
    protected $guarded = [];
    protected $table = "pim_product_variant_options";

    public function options()
    {
        return $this->belongsTo(PimAttributeOption::class, 'option_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(PimProductVariant::class, 'variant_id', 'id')
            ->where('status', Constant::Yes);
    }

    public function attributes()
    {
        return $this->belongsTo(PimAttribute::class, 'attribute_id');
    }

    public static function saveProductAttributeOption($data)
    {
        return self::updateOrCreate($data);
    }

    public static function deleteOptionsByVariantIds( $variantIds )
    {
        return self::whereIn('variant_id', $variantIds)->delete();
    }
}
