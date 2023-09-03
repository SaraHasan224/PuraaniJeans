<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Helpers\Constant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class PimBsCategory extends Model
{
    public $table = 'pim_bs_categories';

    protected $fillable = [
         'parent_id', 'name', 'slug', 'position' , 'status', 'image', 'is_featured', 'is_featured_weight'
    ];
    public function getNameAttribute($value)
    {
        return $this->attributes[ 'name' ];
    }

    public function getImageAttribute($value)
    {
        return asset("storage/".$value);
    }

    public function getIconAttribute($value)
    {
        $image = env('ENV_FOLDER') . $value;
        return Helper::getImgixImage($image, false,200);
    }

    public function children()
    {
        return $this->hasMany(PimBsCategory::class, 'parent_id', 'id')
//            ->select('id', 'parent_id')
            ->where('status', Constant::Yes)
            ->with('children');
    }

    public function childBsCategory()
    {
        return $this->hasMany(PimBsCategory::class, 'parent_id', 'id')
            ->where('status', Constant::Yes)
            ->with('childBsCategory');
    }

    public function parent(){
        return $this->belongsTo(PimBsCategory::class, 'parent_id', 'id')->select('id','name', 'slug', 'parent_id')
          ->where('status', Constant::Yes);
    }

    public static function getCategoryBySlug( $slug )
    {
        return self::select('id', 'name', 'slug', 'parent_id')
            ->where('slug', $slug)
            ->where('status', Constant::Yes)
            ->with('parent')
            ->first();
    }

    public static function getFeaturedCategories()
    {
        return self::select('name', 'slug', 'icon', 'product_count')
            ->where('status', Constant::Yes)
            ->where('is_featured', Constant::Yes)
            ->where('product_count', '>' , 0)
            ->orderBy('position')
            ->get()
            ->take(4);
    }

    public static function getCategories( $parentId = 0 )
    {
        return self::select('name', 'slug', 'icon', 'product_count')
            ->where('parent_id', $parentId)
            ->where('status', Constant::Yes)
//            ->where('product_count', '>' , 0)
            ->orderBy('position')
            ->get();
    }

    public static function getAllSubCategoryIds( $slug )
    {
        $allCategories = self::select('id')
            ->where('slug', $slug)
            ->where('status', Constant::Yes)
            ->with('children')
            ->first();

        if($allCategories)
        {
            $allCategories = $allCategories->toArray();
            array_walk_recursive($allCategories, 'self::getRecursiveIds');
            return self::$categoryIds;
        }
        
        return [];
    }

    public static $categoryIds;
    public static $categoryMenuItem;
    public static function getRecursiveIds($value, $key)
    {
        if($key == 'id')
        {
            self::$categoryIds[] = $value;
        }
    }

    public static function getRecursiveMenuItems($category, $key)
    {
        $hasChildCategories = $category->children();
        $menuItem = [
            "title" => $category->name,
            "type" => !empty($hasChildCategories) ? "sub" : "link",
            "children" => !empty($hasChildCategories) ? true : false,
            "megaMenu" => !empty($hasChildCategories) ? true : false,
            "megaMenuType" => !empty($hasChildCategories) && $hasChildCategories->count() < 7 ? 'small' : ($hasChildCategories->count() < 12 ? "medium" : "large"),
            "path" => `/shop?category=men&brand=&color=&size=&minPrice=&maxPrice=`
        ];
        if ($menuItem['children']) {
            $menuItem['children'] = [];
        };
        self::$categoryMenuItem[] = $menuItem;
    }

    public static function getAllMenuItems()
    {
        $allCategories = self::select('id',"name", "slug","product_count","position")
            ->where('status', Constant::Yes)
            ->where('parent_id', Constant::No)
            ->with('children:id,parent_id,name,slug,product_count,position')
            ->get();
        $allCategoriesTransformed = $allCategories->map(function ($item) {
                $childCategories = $item->children()->get();
                $childCategoryCount = $childCategories->count();
                $megaMenuType = "";
//                if ($childCategoryCount > 12 && $childCategories->count() < 18) {
//                    $megaMenuType = "small";
//                }else
                if($childCategories->count() > 12) {
                    $megaMenuType = $childCategories->count() < 30 ? "medium" : "large";
                }
                $menuItem = [
                    "title" => $item->name,
                    "parent_title" => '',
                    "child_title" => '',
                    "type" => $childCategoryCount > 0 ? "sub" : "link",
                    "children" => $childCategoryCount > 0 ? true : false,
                    "megaMenu" => $childCategoryCount > 12 ? true : false,
                    "megaMenuType" => $megaMenuType,
                    "path" => $childCategoryCount > 0 ? '' : $item->slug
                ];
                if($childCategoryCount > 0) {
                    $menuItem['children'] =  $childCategories->map(function ($subItem) use($menuItem) {
                        $subChildCategories = $subItem->children()->get();
                        $subChildCategoryCount = $subItem->count();
                        $subMenuItem = [
                            'count' => $subChildCategoryCount,
                            "title" => $subItem->name,
                            "parent_title" => $menuItem['title'],
                            "child_title" => '',
                            "type" => "link",
                            "children" => $subChildCategoryCount > 0 ? Constant::Yes : Constant::No,
                            "megaMenuType" => !empty($subChildCategoryCount) && $subChildCategoryCount < 7 ? 'small' : ($subChildCategoryCount < 12 ? "medium" : "large"),
                            "path" => $subItem->slug,
                            "tag" => $subItem->position < 5 ? "new" : "",
                        ];

                        $subMenuItemChild = [];
                        if($subChildCategoryCount > 0) {
                            $subMenuItem['children'] = $subChildCategories->map(function ($subChildItem, $key) use($menuItem, $subMenuItem, $subMenuItemChild) {
                                $subSubChildCategoryCount = $subChildItem->count();
                                $subMenuItemChild[$key] = [
                                    'count' => $subSubChildCategoryCount,
                                    "title" => $subChildItem->name,
                                    "child_title" => $subMenuItem['title'],
                                    "parent_title" => $menuItem['title'],
                                    "type" => "link",
                                    "children" => Constant::No,
                                    "megaMenuType" => !empty($subSubChildCategoryCount) && $subSubChildCategoryCount < 7 ? 'small' : ($subSubChildCategoryCount < 12 ? "medium" : "large"),
                                    "path" => $subChildItem->slug,
                                    "tag" => $subChildItem->position < 3 ? "new" : "",
                                ];
                                return $subMenuItemChild[$key];
                            });
                            return $subMenuItem;
                        }else {
                            return $subMenuItem;
                        }
                    });
                }
                return $menuItem;
            })
            ->toArray();
//        [
//            'CategoriesTransformed' => $allCategoriesTransformed,
//            'allCategories' => $allCategories,
//        ];
        return $allCategoriesTransformed;
    }

    public static function getAllProductCategories()
    {
        $allCategories = self::select('id',"name", "slug")
            ->where('status', Constant::Yes)
            ->where('parent_id', Constant::No)
            ->with('children:id,parent_id,name,slug')
            ->get();
        $allCategoriesTransformed = $allCategories->map(function ($item) {
            $childCategories = $item->children()->get();
            $childCategoryCount = $childCategories->count();
            $megaMenuType = "";
//                if ($childCategoryCount > 12 && $childCategories->count() < 18) {
//                    $megaMenuType = "small";
//                }else
            if($childCategories->count() > 12) {
                $megaMenuType = $childCategories->count() < 30 ? "medium" : "large";
            }
            $menuItem = [
                "label" => $item->name,
                "value" => $item->slug,
                "children" => $childCategoryCount > 0 ? true : false,
            ];
            if($childCategoryCount > 0) {
                $menuItem['children'] =  $childCategories->map(function ($subItem) use($menuItem) {
                    $subChildCategories = $subItem->children()->get();
                    $subChildCategoryCount = $subItem->count();
                    $subMenuItem = [
                        "label" => $subItem->name,
                        "value" => $subItem->slug,
                        "children" => $subChildCategoryCount > 0 ? Constant::Yes : Constant::No,
                    ];

                    $subMenuItemChild = [];
                    if($subChildCategoryCount > 0) {
                        $subMenuItem['children'] = $subChildCategories->map(function ($subChildItem, $key) use($menuItem, $subMenuItem, $subMenuItemChild) {
                            $subMenuItemChild[$key] = [
                                "label" => $subChildItem->name,
                                "value" => $subChildItem->slug,
                                "children" => Constant::No,
                            ];
                            return $subMenuItemChild[$key];
                        });
                        return $subMenuItem;
                    }else {
                        return $subMenuItem;
                    }
                });
            }
            return $menuItem;
        })
            ->toArray();
//        [
//            'CategoriesTransformed' => $allCategoriesTransformed,
//            'allCategories' => $allCategories,
//        ];
        return $allCategoriesTransformed;
    }


    public static function getAllFeaturedCategories()
    {
        $categories = self::select('id',"name", "slug","product_count","position", "image")
            ->where('status', Constant::Yes)
            ->where('parent_id', Constant::No)
            ->where('is_featured', Constant::Yes)
            ->orderBy('is_featured_weight', "DESC")
            ->get();

        $featuredCategoryId = self::select('id')
            ->where('status', Constant::Yes)
            ->where('parent_id', Constant::No)
            ->where('is_featured', Constant::Yes)
            ->orderBy('is_featured_weight', "DESC")->first();

        $allCategories = $categories->map(function ($item) use($featuredCategoryId) {
            $menuItem = [
                'index' => $item->id,
                'image' => $item->image,
                'text' => $item->name,
                'slug' => $item->slug,
                'product_count' => 70,//$item->product_count,
                'is_centered' => $featuredCategoryId->id == $item->id ? Constant::Yes : Constant::No,
            ];
            return $menuItem;
        })
            ->toArray();
        return $allCategories;
    }

}
