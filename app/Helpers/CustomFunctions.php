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

