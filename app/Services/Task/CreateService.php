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

    public function attachCustomFieldsByRoute(int $task_id, $routeName, $request)
    {
        $task = Task::with('category.custom_fields')->find($task_id);
        foreach ($task->category->custom_fields()->where('route', $routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? (Arr::get($request, str_replace(' ', '_', $data->name)) ?? [null]): [];
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
        return $task;
    }


}
