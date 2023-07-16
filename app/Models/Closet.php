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
}
