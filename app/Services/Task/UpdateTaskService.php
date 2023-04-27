<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\CustomService;
use App\Services\Traits\Response;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateTaskService
{
    use Response;

    /**
     * @var CreateService
     */
    private CreateService $service;
    /**
     * @var CustomFieldService
     */
    private CustomFieldService $custom_field_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }

    /**
     * @param $task_id
     * @param $data
     * @return RedirectResponse
     */
    public function not_completed_web($task_id, $data): RedirectResponse
    {
        $task = Task::find($task_id);
        $task->update(['status' => Task::STATUS_NOT_COMPLETED, 'not_completed_reason' => $data]);
        Alert::success(__('Успешно сохранено'));
        return back();
    }

}
