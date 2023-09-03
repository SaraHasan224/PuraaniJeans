<?php

namespace App\Models;


use App\Helpers\Constant;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use function Ramsey\Uuid\v4;

class Closet extends Model
{
    protected $table ="closets";

    public function getLogoAttribute($value)
    {
        return asset($value)."?v=".time();
    }
    public function getBannerAttribute($value)
    {
        return asset($value)."?v=".time();
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
        return $this->belongsTo(Customer::class);
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
        'closet_categories' => [
            'closet_ref' => 'required|string|exists:closets,closet_reference',
            'category_slug' => 'required|exists:pim_categories,pim_cat_reference',
        ],
    ];


    public static function findByCustomerId($customerId){
        return self::where('customer_id', $customerId)->first();
    }

    public static function findByReference($ref){
        return self::where('closet_reference', $ref)->first();
    }

    public static function getByFilters($filter)
    {
        $data = self::select('id', 'customer_id', 'closet_name', 'closet_reference', 'status', 'logo', 'banner', 'is_trending', 'trending_position', 'created_at','updated_at');
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
//            $query->where('is_trending', Constant::Yes)
//                  ->orderBy('trending_position', 'DESC');
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
            'about_closet' => $requestData['about'],
            'closet_name' => $requestData['name'],
            'closet_reference' => v4(),
            'status' => Constant::CLOSET_STATUS['enabled'],

        ];
        $closet = self::create($data);

        $logoFile = $requestData['logo'];
        $logoFileName = Helper::clean(trim(strtolower($closet->closet_name)));
        $logoFilePath = "images/closets/" . $closet->id . "/logo/" ;
        $logoImage = Helper::uploadFileToApp($logoFile, $logoFileName, $logoFilePath);

        $bannerFile = $requestData['banner'];
        $bannerFileName = Helper::clean(trim(strtolower($closet->closet_name)));
        $bannerFilePath = "images/closets/" . $closet->id . "/banner/" ;
        $bannerImage = Helper::uploadFileToApp($bannerFile, $bannerFileName, $bannerFilePath);

        $closet->update([
            'logo' => $logoImage,
            'banner' => $bannerImage,
        ]);
        $closet->fresh();
        return $closet;
    }

    public static function updateCloset( $reference, $requestData )
    {
        $closet = Closet::findByReference($reference);

        $logoImage = $closet->logo;
        $logoFile = $requestData['logo'];
        if(!empty($logoImage)) {
            $logoFileName = Helper::clean(trim(strtolower($closet->closet_name)));
            $logoFilePath = "images/closets/" . $closet->id . "/logo/";
            $file = new Filesystem();
            $file->cleanDirectory(public_path($logoFilePath));
            $logoImage = Helper::uploadFileToApp($logoFile, $logoFileName, $logoFilePath);
        }
        $bannerFile = $requestData['banner'];
        $bannerImage = $closet->banner;
        if(!empty($bannerFile)) {
            $bannerFileName = Helper::clean(trim(strtolower($closet->closet_name)));
            $bannerFilePath = "images/closets/" . $closet->id . "/banner/" ;
            $file = new Filesystem();
            $file->cleanDirectory(public_path($bannerFilePath));

            $bannerImage = Helper::uploadFileToApp($bannerFile, $bannerFileName, $bannerFilePath);
        }

        $closet->update([
            'logo' => $logoImage,
            'banner' => $bannerImage,
            'about_closet' => $requestData['about'],
            'closet_name' => $requestData['name'],
        ]);
        $closet->fresh();

        return $closet;
    }

}
