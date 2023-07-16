<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\Misc;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Country extends Model
{

    protected $table = 'countries';
    protected $primaryKey = 'id';

    public function getNameAttribute($val)
    {
        return ucwords($val);
    }

    protected function rules($except_id = "")
    {
        $arr = array(
            'currency_code' => 'required'
        );
        return $arr;
    }

    public function provinces()
    {
        return $this->hasMany(Province::class ,'country_id', 'country_id' )
            ->orderBy('name', 'ASC');
    }

    public function cities()
    {
        return $this->hasMany('App\Models\City', 'fk_country', 'country_id');
    }


    protected function updateCountry($request, $id)
    {
        // $is_shippable	= (empty($request['is_ship'])?'0':'1');
        // $vat			= (empty($request['vat'])?0:$request['vat']);
        // $currency		= Currency::getCurrencybyCode($request['currency_code']);
        // $country						= $this->find($id);
        // $country->fk_region				= $request['fk_region'];
        // $country->currency_code			= $request['currency_code'];
        // $country->vat					= $vat;
        // $country->is_ship				= $is_shippable;
        // $country->fk_currency_display	= $currency->currency_id;
        // $country->status				= $request['status'];
        // $country->save();
    }

    protected function getCountryNameById($id)
    {
        return $this->where('id', $id)->pluck('name')->first();
    }
    public static function getEnabledCountries()
    {
        return Country::where('status', Constant::Yes)->orderBy('name')->pluck('code')->toArray();
    }

    public static function getCountryByCountryCode($countryCode , $pluckCountryId = false)
    {
        $column = [];
        $data = self::where('code', $countryCode);
        if($pluckCountryId){
            $column = [
                'id',
                'code'
            ];
            return $data->select($column)->first();
        }else{
            return $data->where('status', Constant::Yes)->first();
        }
    }
}
