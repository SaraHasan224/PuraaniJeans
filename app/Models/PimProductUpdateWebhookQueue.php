<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class PimProductUpdateWebhookQueue extends Model
{
    protected $guarded = [];
    protected $table = 'pim_product_update_webhook_queue';

    protected function getNonProcessedProducts()
    {
        return self::where('is_processed', Constant::No)->get();
    }
}