<?php

namespace App\Services;

use App\Item\HomeItem;
use App\Models\Task;
use TCG\Voyager\Models\Category;

class HomeService
{
    public function home()
    {
        $item = new HomeItem();
        $item -> categories = Category::where('parent_id', null)->orderBy("order", "asc")->get();
        $item -> tasks  =  Task::query()->where('status', Task::STATUS_OPEN)->orWhere('status',Task::STATUS_RESPONSE)->orderBy('id', 'desc')->get();
        $item -> child_categories = Category::where('parent_id','!=',null)->orderBy("order", "asc")->get();
        return $item;

    }
}
