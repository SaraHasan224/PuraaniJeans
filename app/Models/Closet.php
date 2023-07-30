<?php

namespace App\Models;


use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use function Ramsey\Uuid\v4;

class Closet extends Model
{
    protected $table ="closets";

    public function getLogoAttribute($value)
    {
        return asset("storage/".$value);
    }
    public function getBannerAttribute($value)
    {
        return asset("storage/".$value);
    }

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

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }
    public function products()
    {
        return $this->hasMany(PimProduct::class, 'closet_id', 'id');
    }
//    public function orders()
//    {
//        return $this->hasMany(Customer::class, 'closet_id', 'id');
//    }

    public static $validationRules = [
        'image-upload' => [
            'banner' => 'required',
            'icon' => 'required',
        ],
        'storeCategories' => [
            'closet_ref' => 'required|string|exists:closets,closet_reference',
            'category_slug' => 'required|exists:pim_categories,pim_cat_reference',
        ],
    ];


    public static function findByReference($ref){
        return self::where('closet_reference', $ref)->first();
    }

    public static function getByFilters($filter)
    {
        $data = self::select('id', 'customer_id', 'closet_name', 'closet_reference', 'status', 'logo', 'banner', 'created_at','updated_at','deleted_at');
        $data = $data->orderBy('id', 'DESC');

        if (count($filter))
        {
            if (!empty($filter['closet_name']))
            {
                $data = $data->where('closet_name', 'LIKE', '%' . trim($filter['name']) . '%');
            }
        }

        $count = $data->count();

//        if (isset($filter['start']) && isset($filter['length']))
//        {
//            $data->skip($filter['start'])->limit($filter['length']);
//        }

        return [
            'count'   => $count,
            'offset'  => isset($filter['start']) ? $filter['start'] : 0,
            'records' => $data->get()
        ];
    }

    public static function getClosetListing($perPage = "", $type, $disablePagination = false)
    {
        $fields = [
            'id',
            'customer_id',
            'closet_name',
            'logo',
            'closet_reference',
        ];
        $query = self::select($fields)->where('status', Constant::Yes);
//
        $query->whereHas('customer', function($query) {
            $query->where('status',Constant::Yes);
        });
        if($type == Constant::PJ_CLOSETS_LIST_TYPES['Trending']) {
            $query->where('is_trending', Constant::Yes)
                  ->orderBy('trending_position', 'DESC');
        }else {
            $query->orderBy('closet_name', 'ASC');

        }

        $closetList = $query
            ->whereHas('products')
            ->paginate($perPage);

        $closetTransformed = $closetList
            ->getCollection()
            ->map(function ($item) use($type){
                if($type == Constant::PJ_CLOSETS_LIST_TYPES['Trending']){
                    $item['country'] = $item->customer->country->name;
                }
                unset($item->customer);
                unset($item->id);
                unset($item->customer_id);
                return $item;
            })->toArray();
        if($disablePagination) {
            return $closetTransformed;
        }
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $closetTransformed,
            $closetList->total(),
            $closetList->perPage(),
            $closetList->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $closetList->currentPage()
                ]
            ]
        );
    }

    public static function createCloset( $requestData )
    {
        $data = [
            'customer_id' => $requestData['customer_id'],
            'closet_name' => $requestData['name'],
            'closet_reference' => v4(),

        ];

        return self::create($data);
    }

}
