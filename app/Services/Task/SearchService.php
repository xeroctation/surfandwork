<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Models\Task;
use App\Models\Category;
use App\Models\TaskResponse;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SearchService
{
    public const MAX_SEARCH_TASK = 20;
    public const REMOTE_TASK = 1;

    public function task_service($auth_response, $userId, $task, $filter): SearchServiceTaskItem
    {
        if (auth()->check() && $userId !== $task->user_id) {
            $viewed_tasks = Cache::get('user_viewed_tasks' . $userId) ?? [];
            if (!in_array($task->id, $viewed_tasks)) {
                $viewed_tasks[] = $task->id;
            }
            Cache::put('user_viewed_tasks' . $userId, $viewed_tasks);

            $task->views++;
            $task->save();
        }
        $item = new SearchServiceTaskItem();
        $item->selected = $task->responses()->where('performer_id', $task->performer_id)->first();
        $item->responses = $item->selected ? $task->responses()->where('id', '!=', $item->selected->id) : $task->responses();
        $item->auth_response = $auth_response ? $task->responses()->where('performer_id', $userId)->with('user')->first() : null;
        $item->same_tasks = $task->category->tasks()->where('id', '!=', $task->id)->where('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'desc')->get();
        $item->addresses = $task->addresses;
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->respons_reviews = Review::query()->where('task_id',$task->id)->get();
        $item->responses = match ($filter) {
            'rating' => TaskResponse::query()->join('users', 'task_responses.performer_id', '=', 'users.id')
                ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.review_rating')->get(),
            'date' => $item->responses->orderByDesc('created_at')->get(),
            'reviews' => TaskResponse::query()->join('users', 'task_responses.performer_id', '=', 'users.id')
                ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.reviews')->get(),
            default => $item->responses->get(),
        };
        $value = Carbon::parse($task->created_at)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Today" : "$value->day-$value->monthName";
        $item->created = "$day  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->end_date)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $item->end = "$value->day-$value->monthName  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->start_date)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Today" : "$value->day-$value->monthName";
        $item->start = "$day  $value->noZeroHour:$minut";
        return $item;
    }

    public function search_new(?string $lang = 'en') {
        $categories = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations('en')->orderBy("order")->get();
        });

        $allCategories['categories'] = collect($categories)->where('parent_id', null)->all();

        $allCategories['categories2'] = collect($categories)->where('parent_id', '!=', null)->all();

        return $allCategories;
    }
}
