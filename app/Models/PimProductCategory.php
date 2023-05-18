<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PimProductCategory extends Model
{

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(PimCategory::class, 'category_id', 'id');
    }

    public static function mapProductCategory($productId, $categoryId)
    {
        return self::updateOrCreate([
          'product_id'    => $productId,
          'category_id'   => $categoryId
        ]);
    }

    public static function deleteMappings( $productId, $categoryIds = [] )
    {
        return self::where('product_id', $productId)
          ->whereNotIn('category_id', $categoryIds)
          ->delete();
    }

}
