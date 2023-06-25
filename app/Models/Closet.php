<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Closet extends Model
{
    protected $table ="closets";

    protected $fillable = [
        'customer_id',
        'closet_name' ,
        'closet_reference',
        "about_closet",
        'status',
        'logo',
        'logo_public_path',
        'banner',
        'banner_public_path',
    ];
}
