<?php


namespace App\Services\Task;


use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\WalletBalance;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ResponseService
{
    /**
     *
     * Function  store
     * This method works when responding to a task
     * @param $data
     * @param $task
     * @param $auth_user
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store($data, $task, $auth_user): array
    {

        if ((int)$task->user_id === (int)$auth_user->id) {
            abort(403, "It is your task");
        }
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['performer_id'] = $auth_user->id;
        /** @var WalletBalance $balance */
        $balance = WalletBalance::query()->where('user_id', $auth_user->id)->first();
        if ($balance) {
            switch (true){
                case $task->responses()->where('performer_id', $auth_user->id)->first() :
                    $success = false;
                    $message = __('Уже было');
                    break;
                default :
                    $success = true;
                    $message = __('Выполнено успешно');
                    TaskResponse::query()->create($data);
                    break;
            }
        } else {
            $success = false;
            $message = __('Недостаточно баланса');
        }

        return compact('success', 'message');

    }

    /**
     *
     * Function  selectPerformer
     * This method works when one performer is selected from the responses
     * @param $response
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \JsonException
     */
    #[ArrayShape(['success' => "bool", 'message' => "mixed", 'data' => "array"])]
    public function selectPerformer($response): array
    {
        $task = $response->task;
        if ($task->status >= 3 || auth()->id() === $response->performer_id ) {
            abort(403, 'No Permission');
        }
        $data = [
            'performer_id' => $response->performer_id,
            'status' => Task::STATUS_IN_PROGRESS
        ];
        $response_user = $response->user;
        $task->update($data);
        $performer = $response->performer;
        $data = [
            'performer_name' => $performer->name,
            'performer_phone' => $performer->phone_number,
            'performer_description' => $performer->description,
            'performer_avatar' => asset('storage/' . $performer->avatar),
        ];
        TaskResponse::query()->where(['task_id' => $task->id, 'not_free' => 0])->where('performer_id', '!=', $performer->id)->delete();
        return ['success' => true,'message' => __('Выполнено успешно'), 'data' => $data];
    }

}
