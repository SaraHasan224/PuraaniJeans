<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class MerchantBranding extends Model
{
    protected $table = 'merchant_branding';

    public function getFaviconAttribute($value)
    {
        if (empty($value))
        {
            $bsecure_logo = env('BSECURE_FAVICON_PATH');
            return Helper::getImgixImage($bsecure_logo);
        }
        return Helper::getImgixImage($value, false,200);
    }

    public function getWebsiteAttribute($value)
    {
        if (empty($value))
        {
            $bsecure_logo = env('BSECURE_LOGO_PATH');
            return Helper::getImgixImage($bsecure_logo);
        }
        return Helper::getImgixImage($value, false,200);
    }

    public function getPaymentGatewayIconAttribute($value)
    {
        if (empty($value))
        {
            return null;
        }
        return Helper::getImgixImage($value, false);
    }
}
