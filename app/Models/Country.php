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

    protected function rules($except_id = "")
    {
        $arr = array(
            'currency_code' => 'required'
        );
        return $arr;
    }

    protected function getAllCountriesWithRegion()
    {
        return $this->with(array('region'))->get();
    }

    protected function getCountryNameById($id)
    {
        return $this->where('id', $id)->pluck('name')->first();
    }

    protected function pluckCountriesByRegionId($region_id)
    {
        return $this->where('fk_region', $region_id)->pluck('id');
    }

    protected function getSingleCountryById($id)
    {
        return $this->with(array('region'))->find($id);
    }

    protected function getSingleCountryByName($country_name)
    {
        return $this->where('name', $country_name)->first();
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'fk_region', 'region_id');
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

    protected function getAllCountries($only_shippable = false)
    {
        $countries = $this->where('enabled', Constant::Yes);
        if ($only_shippable)
        {
            $countries = $countries->where('is_ship', '1');
        }
        $countries = $countries->orderBy('priority', 'desc')->orderBy('sort_order', 'asc')->select('name', 'id', 'country_code')->get()->toArray();

        return $countries;
    }

    public static function getEnabledCountries()
    {
        return Country::where('status', Constant::Yes)->orderBy('name')->pluck('code')->toArray();
    }

    public static function getPhoneNumberMaskByCode($code)
    {
        return Country::select('phone_number_mask')->where('country_code', $code)->where('status', Constant::Yes)->first();
    }

    protected function getCountryByName($location_name)
    {
        return $this->where('name', $location_name)->first();
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

    public static function getCountries()
    {
        return self::where('code', '!=', null)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public static function getMessageBirdOriginatorByCountryCode($countryCode){
        return self::select('msg_bird_originator')->where('country_code', $countryCode)->first()->msg_bird_originator;
    }

}
