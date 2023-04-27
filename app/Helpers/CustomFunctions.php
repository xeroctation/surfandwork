<?php

use App\Models\Category;

function categories()
{
    $datas = Category::with('translations')->orderBy("order")->get();

    $child_categories = [];
    $parent_categories = [];

    foreach ($datas as $data) {
        if ($data->parent_id === null) {
            $parent_categories[] = $data;
        } else {
            $child_categories[] = $data;
        }

    }

    foreach ($parent_categories as $parent_category) {

        foreach ($child_categories as $child_category) {
            if ($parent_category->id == $child_category->parent_id) {
                $categories[$parent_category->id][] = $child_category;
            }

        }

    }


    return $categories;

}

function getLocale()
{
    $locale = app()->getLocale();

    if ($locale === 'en') $locale = 'en_Latn';
    return $locale;

}

function correctPhoneNumber($phone)
{
    return match (true) {
        strlen($phone) == 12 => '+' . $phone,
        strlen($phone) > 13 => substr($phone, 0, 13),
        default => $phone,
    };
}


