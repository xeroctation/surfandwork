<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Requests\CreateNameRequest;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\TaskDateRequest;
use App\Models\CustomField;
use App\Models\Task;
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

    public function custom_get(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CUSTOM);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return redirect()->route("task.create.remote", $task->id);
            }
            return redirect()->route('task.create.address', $task->id);
        }
        return view('create.custom', compact('task', 'custom_fields'));
    }

    public function custom_store(Request $request, int $task_id)
    {
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_CUSTOM, $request->all());

        if ($task->category->parent->remote) {
            return redirect()->route("task.create.remote", $task->id);
        }
        return redirect()->route('task.create.address', $task->id);
    }

    public function address(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        return view('create.location', compact('task', 'custom_fields'));
    }

    public function address_store(Request $request, int $task_id)
    {
        $task = Task::select('id')->find($task_id);
        $requestAll = $request->all();
        $cordinates = $this->service->addAdditionalAddress($task_id, $requestAll);
        $task->update([
            'coordinates' => $cordinates,
            'go_back'=> $request->get('go_back')
        ]);

        $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS, $requestAll);
        return redirect()->route("task.create.date", $task_id);

    }

    public function date(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_DATE);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        return view('create.date', compact('task', 'custom_fields'));

    }

    public function date_store(TaskDateRequest $request, $task_id)
    {
        $data = $request->validated();
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_DATE, $request->all());
        $task->update($data);

        return redirect()->route('task.create.budget', $task_id);
    }

    public function budget(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_BUDGET);
        $category = $result['category'];
        $custom_fields = $result['custom_fields'];
        $task = $result['task'];
        return view('create.budget', compact('task', 'category', 'custom_fields'));
    }

    public function budget_store(int $task_id, BudgetRequest $request)
    {
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_BUDGET, $request->all());

        $task->budget = $request->get('amount2');
        $task->save();

        return redirect()->route('task.create.note', $task->id);
    }


    public function note(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_NOTE);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];

        return view('create.notes', compact('task', 'custom_fields'));
    }

    public function note_store(int $task_id, NoteRequest $request)
    {
        $data = $request->validated();
        if ($request['docs'] === "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }

        $task = Task::select('id')->find($task_id)->update($data);

        return redirect()->route("task.create.contact", $task_id);
    }


    public function images_store(Request $request, Task $task)
    {
        $imgData = json_decode($task->photos) ?? [];
        foreach ($request->file('images') as $uploadedImage) {
            $filename = time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path() . '/storage/uploads/', $filename);
            $imgData[] = $filename;
        }
        $task->photos = $imgData;
        $task->save();
    }

}
