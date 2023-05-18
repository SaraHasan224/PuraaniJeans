<?php

namespace App\Helpers;

class Constant
{
    const timezone = 'Asia/Karachi';

    const unSerializableFields = [];

    const DISCOUNT_TYPE = [
        'flat'       => 1,
        'percentage' => 2,
    ];
    const CUSTOMER_APP_PRODUCT_LISTING = [
        'FEATURED_PRODUCTS'           => 1,
        'CATEGORY_PRODUCTS'           => 2,
        'STORES_PRODUCTS'             => 3,
        'RECENTLY_VIEWED_PRODUCTS'    => 4
    ];

    const CUSTOMER_APP_STORE_LISTING_TYPE = [
        'Featured' => 1,
        'All' => 2,
        'Filters' => 3,
    ];

    const SORT_BY_FILTERS = [
        'featured' => 'Featured',
        'newest_arrival' => 'New Arrival',
        'price_high_to_low' => 'Price:High to Low',
        'price_low_to_high' => 'Price: Low to High'
    ];

    const GeneralError = "messages.general.failed";
    const Yes = 1;
    const No = 0;
    const DEFAULT_VARIANT = 'Default Variant';
}
