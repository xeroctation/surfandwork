<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNameRequest;
use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    protected CreateService $service;
    protected CustomFieldService $custom_field_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }


    public function name(Request $request)
    {
        $category_id = $request->get('category_id');
        $item = $this->service->name($category_id);
        return view("create.name", [
            'current_category'=>$item->current_category,
            'categories'=>$item->categories,
            'child_categories'=>$item->child_categories,
        ]);
    }

    public function name_store(CreateNameRequest $request)
    {
        $data = $request->validated();
        $task_id = $this->service->storeName($data['name'], $data['category_id']);
        return redirect()->route("task.create.custom.get", $task_id);
    }

}
