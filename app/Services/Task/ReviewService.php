<?php

namespace App\Services\Task;

use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ReviewService
{

    /**
     * Send review for a task
     * @param $task
     * @param $request
     * @param bool $status
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function sendReview($task, $request, bool $status = false): void
    {
        switch (true) {
            case $task->user_id === auth()->id() :
                // user review to performer
                if ($status) {
                    $request['status'] = 1;
                }
                $notification = self::userReview($task, $request);

                break;
            case $task->performer_id === auth()->id() :
                // performer review to user
                $notification = self::performerReview($task, $request);

                break;
        }
    }


    /**
     * task performer review
     * @param $task
     * @param $request
     * @throws \JsonException
     */
    public static function performerReview($task, $request)
    {
        Review::query()->create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => $task->performer_id,
            'user_id' => $task->user_id,
            'as_performer' => 1
        ]);

        $user = User::query()->find($task->user_id);
        if ((int)$request->good === 1) {
            $user->increment('review_good');
        } else {
            $user->increment('review_bad');
        }
        $user->increment('reviews');
        $task->performer_review = 1;
        $task->save();

        return $notification;
    }

    /**
     * task user review
     * @param $task
     * @param $request
     * @throws \JsonException
     */
    public static function userReview($task, $request)
    {
        $task->status = $request->status ? Task::STATUS_COMPLETE : Task::STATUS_NOT_COMPLETED;
        $task->save();
        $performer = User::query()->find($task->performer_id);
        if ((int)$request->good === 1) {
            $performer->increment('review_good');
        } else {
            $performer->increment('review_bad');
        }
        $performer->increment('reviews');
        Review::query()->create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => $task->user_id,
            'user_id' => $task->performer_id,
        ]);

        return $notification;
    }
}
