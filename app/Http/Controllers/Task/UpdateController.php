<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Task;
use App\Services\Task\ReviewService;
use App\Services\Task\UpdateTaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UpdateController extends Controller
{

    protected UpdateTaskService $updateTaskService;

    public function __construct()
    {
        $this->updateTaskService = new UpdateTaskService();
    }
    /**
     *
     * Function  not_completed
     * @param Request $request
     * @param int $task_id
     * @return  mixed
     */
    public function not_completed(Request $request, int $task_id): mixed
    {
        $request->validate(['reason' => 'required']);
        $data = $request->get('reason');
        return $this->updateTaskService->not_completed_web($task_id, $data);
    }

    /**
     *
     * Function  sendReview
     * @param Task $task
     * @param Request $request
     * @return  mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendReview(Task $task, Request $request): mixed
    {
        try {
            ReviewService::sendReview($task, $request, true);
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return back();
    }
}
