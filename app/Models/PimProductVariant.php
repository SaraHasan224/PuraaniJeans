<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Constant;

class PimProductVariant extends Model
{
    protected $guarded = [];
    protected $table = 'pim_product_variants';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'discounted_price',
        'ultimate_best_discount_type',
        'ultimate_best_discount',
        'ultimate_discount',
        'ultimate_best_price',
        'ultimate_best_discounted_price',
    ];

    public function getDiscountedPriceAttribute(){
        $price = $this->price;
        $discount = $this->discount;
        $discountedPrice = 0;

        if( $this->discount_type == Constant::DISCOUNT_TYPE['percentage'] )
        {
            return $price - (( $discount / 100 ) * $price);
        }
        else if( $this->discount_type == Constant::DISCOUNT_TYPE['flat'] )
        {
            return $price - $discount;
        }
        return $discountedPrice;
    }

    public function getUltimateBestDiscountTypeAttribute(){
        $discountType = $this->discount_type;
        $isCampaignEnabled = $this->product->campaign_sale_enabled == 1;
        if($isCampaignEnabled) {
            $discountType = $this->best_discount_type; // 2
        }
        return $discountType;
    }

    public function getUltimateBestDiscountAttribute(){
        $price = $this->price;
        $productDiscountedPrice = $this->discounted_price;
        $isCampaignEnabled = $this->product->campaign_sale_enabled == 1;
        if($isCampaignEnabled) {
            $discountType = $this->best_discount_type; // 2
            $discount = $this->best_discount_value; //
            if( $discountType == Constant::DISCOUNT_TYPE['percentage'] )
            {
                $discount = ( $discount / 100 ) * $productDiscountedPrice;
            }
        }else {
            $discount = $this->discount;
            if( $this->discount_type == Constant::DISCOUNT_TYPE['percentage'] )
            {
                $discount = (( $discount / 100 ) * $price);
            }
        }
        return $discount;
    }

    public function getUltimateDiscountAttribute(){
        $discount = $this->discount;
        $isCampaignEnabled = $this->product->campaign_sale_enabled == 1;
        if($isCampaignEnabled) {
            $discount = $this->best_discount_value; //
        }
        return $discount;
    }

    public function getUltimateBestPriceAttribute(){
        $productPrice = $this->price;
        $isCampaignEnabled = $this->product->campaign_sale_enabled == 1;
        if($isCampaignEnabled) {
            $productPrice = $this->discounted_price;
        }
        return $productPrice;
    }

    public function getUltimateBestDiscountedPriceAttribute(){
        $productPrice = $this->price;
        $isCampaignEnabled = $this->product->campaign_sale_enabled == 1;
        if($isCampaignEnabled) {
            $productPrice = $this->discounted_price;
        }
        return $productPrice - $this->ultimate_best_discount;
    }

    public function getPriceAttribute($value)
    {
        return ceil($value);
    }

    public function getDiscountAttribute($value)
    {
        return ceil($value);
    }

    public function product()
    {
        return $this->belongsTo(PimProduct::class, 'product_id');
    }
    
    public function image()
    {
        return $this->belongsTo(PimProductImage::class, 'image_id', 'id');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->where('is_active', Constant::Yes)->first();
    }

    public static function findById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByProductAndVariantId($productId, $id, $checkActiveState = true)
    {
        $query = self::where('product_id', $productId)->where('id', $id);
        if($checkActiveState){
            $query->where('is_active', Constant::Yes);
        }
        return $query->first();
    }

    public function options()
    {
        return $this->hasMany(PimProductVariantOption::class,'variant_id');
    }

    public static function getByImportedVariantId($importedVariantId, $storeId)
    {
        return self::where('imported_variant_id', $importedVariantId)
            ->whereHas('product', function ($records) use ($storeId)
            {
                $records->where('store_id', $storeId);
            })
            ->first();
    }

    public static function isUsedSKU($productId, $variant)
    {
        return self::where('product_id', $productId)
          ->where('sku', $variant['sku'])
          ->where('imported_variant_id', '!=', $variant['id'])
          ->count();
    }

    public static function variantsCount($productId)
    {
        return self::where('product_id', $productId)->count();
    }

    public static function saveVariant($variant)
    {
        return self::updateOrCreate(
          [
            'product_id' => $variant['product_id'],
            'imported_variant_id' => $variant['imported_variant_id']
          ],
          [
            'product_variant'   => $variant['product_variant'],
            'price'             => $variant['price'],
            'sku'               => $variant['sku'],
            'quantity'          => $variant['quantity'],
            'track_quantity'    => $variant['track_quantity'],
            'discount'          => $variant['discount'],
            'image_id'          => $variant['image_id'],
            'position'          => $variant['position'] ?? 0,
            'is_active'         => $variant['is_active'],
            'short_description' => $variant['short_description']
          ]
        )->id;
    }

    public static function deleteVariants( $productId, $variantIds = [] )
    {
        $ids = self::where('product_id', $productId)
          ->where(function ($query) use($variantIds) {
              $query->whereNotIn('imported_variant_id', $variantIds)->orWhereNull('imported_variant_id');
          });

        $returnIds = $ids->pluck('id');
        $ids->delete();
        return $returnIds;
    }

    public static function getDefaultVariant($productId)
    {
        return self::where('product_id', $productId)
          ->where('product_variant', Constant::DEFAULT_VARIANT)
          ->first();
    }

    public static function saveDefaultVariant($productId, $request){
        $imageId = PimProductImage::getDefaultProductImage($productId);
        $data = [
          'product_id' => $productId,
          'product_variant' => Constant::DEFAULT_VARIANT,
          'price' => Helper::formatNumber($request['price']),
          'sku' => $request['sku'],
          'quantity' => $request['quantity'] ?? 0,
          'discount' => $request['discount'] ?? 0,
          'discount_type' => $request['discount_type'] ?? 1,
          'image_id' => $imageId ?? 0,
          'position' => Constant::Yes,
          'is_active' => $request['is_active'] ?? Constant::Yes,
          'track_quantity' => $request['track_quantity'] ?? Constant::No,
          'text_note_for_shipment' => $request['text_note_for_shipment'] ?? null,
        ];

        self::create($data);
    }
}
