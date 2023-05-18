<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Authenticatable
{
    protected $table = 'merchants';
    use Notifiable, HasApiTokens;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}


