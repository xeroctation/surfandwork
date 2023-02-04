<?php

namespace App\Services\Task;

use App\Http\Resources\NotificationResource;
use App\Item\CreateNameItem;
use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

class CreateService
{

    public function name($category_id): CreateNameItem
    {
        $item = new CreateNameItem();
        $item->current_category = Category::query()->findOrFail($category_id);
        $item->categories = Category::query()->where('parent_id', null)
            ->select('id', 'name', 'slug')->orderBy("order")->get();
        $item->child_categories = Category::query()->where('parent_id', '<>', null)
            ->select('id', 'parent_id', 'name')->orderBy("order")->get();
        return $item;
    }

    public function storeName($name, $category_id) {
        $data = ['name' => $name, 'category_id' => $category_id];
        $task = Task::query()->create($data);
        $task->category->custom_fields()->where('route', CustomField::ROUTE_NAME,)->get();
        return $task->id;
    }


}
