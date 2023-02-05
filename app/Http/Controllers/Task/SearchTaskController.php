<?php

namespace App\Http\Controllers\Task;

use App\Models\TaskElastic;
use App\Models\Task;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use TCG\Voyager\Models\Category;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Services\Task\SearchService;
use Jenssegers\Agent\Agent;

class SearchTaskController extends VoyagerBaseController
{
    private SearchService $service;

    public function __construct()
    {
        $this->service = new SearchService();
    }

    public function task(Task $task, Request $request): Factory|View|Application
    {
        if (!$task->user_id) {
            abort(404);
        }

        $userId = auth()->id();
        $auth_response = auth()->check();
        $filter = $request->get('filter');
        $item = $this->service->task_service($auth_response, $userId, $task, $filter);

        return view('task.detailed-tasks',
            [
                'review_description' => $item->review_description, 'task' => $task,
                'created' => $item->created, 'end' => $item->end, 'start' => $item->start,
                'complianceType' => $item->complianceType, 'same_tasks' => $item->same_tasks,
                'auth_response' => $item->auth_response, 'selected' => $item->selected,
                'responses' => $item->responses, 'addresses' => $item->addresses,
                'top_users' => $item->top_users, 'respons_reviews' => $item->respons_reviews
            ]);
    }

    public function task_map(Task $task)
    {
        return $task->addresses;
    }
}
