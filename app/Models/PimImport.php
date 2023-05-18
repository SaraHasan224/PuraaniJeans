<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PimImport extends Model
{
    protected $guarded = [];

    public static function addBatch($batchId)
    {
        return self::create(['batch_id' => $batchId]);
    }
}









