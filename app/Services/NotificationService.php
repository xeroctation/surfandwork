<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Mail\MessageEmail;
use App\Models\Notification;
use App\Models\UserCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Events\SendNotificationEvent;
use App\Http\Resources\NotificationResource;

class NotificationService
{


    /**
     * Send new task notification by websocket, sms and firebase
     *
     * @param $task // Task model object
     * @param  $user_id
     * @return void
     */
    public static function sendTaskNotification($task, $user_id): void
    {
        $performers = User::query()->where('role_id', User::ROLE_PERFORMER)->select('id', 'email', 'firebase_token', 'sms_notification', 'email_notification', 'phone_number')->get();
        $performer_ids = [];
        foreach ($performers as $performer) {
            $user_cat_ids = UserCategory::query()->where('user_id', $performer->id)->pluck('category_id')->toArray();
            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $user_id,
                'performer_id' => $performer->id,
                'description' => 'description',
                'task_id' => $task->id,
                "cat_id" => $task->category_id,
                "name_task" => $task->name,
                "type" => Notification::TASK_CREATED
            ]);
            $locale = cacheLang($performer->id);
            $price = number_format($task->budget, 0, '.', ' ');
            self::pushNotification($performer, [
                'title' => self::titles($notification->type),
                'body' => self::descriptions($notification)
            ], 'notification', new NotificationResource($notification));

            self::sendNotificationRequest([$performer->id], [
                'created_date' => $notification->created_at->format('d M'),
                'title' => self::titles($notification->type),
                'url' => route('show_notification', [$notification]),
                'description' => self::descriptions($notification)
            ]);

            if (in_array($task->category_id, $user_cat_ids, true)) {
                $performer_ids[] = $performer->id;

                $notification->save();

                $subject = __('Новая задания', [], $locale);
                $message = $subject . "\n" . __('task_name  №task_id с бюджетом до task_budget', [
                        'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price
                    ], $locale);
                if ($performer->sms_notification) {
                    $phone_number = $performer->phone_number;
                    SmsMobileService::sms_packages(correctPhoneNumber($phone_number), $message);
                }
                info(json_encode($performer));
                if ($performer->email_notification) {
                    $task_id = $task->id;
                    Mail::to($performer->email)->send(new MessageEmail($task_id, $message, $subject));
                }
            }
        }

    }


    /**
     * Function for use send websocket notification by user ids
     *
     * @param $user_ids // User ids array
     * @param  $data
     * @return void
     */
    public static function sendNotificationRequest($user_ids, $data): void
    {
        foreach ($user_ids as $user_id) {
            broadcast(
                new SendNotificationEvent(json_encode($data, true), $user_id)
            )->toOthers();
        }
    }

    /**
     * Function for use send push(firebase) notifications
     *
     * @param $user // User model
     * @param $notification // Notification title and body
     * @param $type // for notification or chat. Values - e.g. "chat", "notification"
     * @param $model // data for handling in mobile
     */
    public static function pushNotification(User $user, $notification, $type, $model): void
    {
        $notification['sound'] = "default";
        foreach ($user->sessions as $session) {

            if (!empty($session->firebase_token))
                Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . env('FCM_SERVER_KEY')
                ])->post('https://fcm.googleapis.com/fcm/send',
                    [
                        "to" => $session->firebase_token,
                        "notification" => $notification,
                        "data" => [
                            "type" => $type,
                            "data" => $model,
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                            "sound" => "default",
                        ],
                        "sound" => "default",
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ]
                )->body();
        }
    }

    public static function titles($type, $locale = null): string
    {
        return match ($type) {
            Notification::TASK_CREATED => __('Новое задание', [], $locale),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Новости', [], $locale),
            Notification::GIVE_TASK => __('Предложение', [], $locale),
            Notification::RESPONSE_TO_TASK => __('Отклик к заданию', [], $locale),
            Notification::SEND_REVIEW => __('Задание выполнено', [], $locale),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем', [], $locale),
            Notification::SEND_REVIEW_PERFORMER => __('Новый отзыв', [], $locale),
            Notification::RESPONSE_TO_TASK_FOR_USER => __('Новый отклик', [], $locale),
            Notification::CANCELLED_TASK, Notification::ADMIN_CANCEL_TASK => __('3адание отменено', [], $locale),
            Notification::ADMIN_COMPLETE_TASK => __('Задания завершено', [], $locale),
            Notification::NEW_PASSWORD => __('Установить пароль', [], $locale),
            Notification::WALLET_BALANCE => __('Дополнительный бонус', [], $locale),
            default => 'Title',
        };
    }

    public static function descriptions($notification, $locale = null): string
    {
        return match ($notification->type) {
            Notification::TASK_CREATED => __('task_name  №task_id с бюджетом до task_budget', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
                'budget' => number_format($notification->task?->budget, 0, '.', ' ')
            ], $locale),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Важные новости и объявления для вас', [], $locale),
            Notification::GIVE_TASK => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ], $locale),
            Notification::RESPONSE_TO_TASK => __('task_name №task_id отправлен', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id
            ], $locale),
            Notification::SEND_REVIEW => __('Заказчик сказал, что вы выполнили эго задачу task_name №task_id и оставил вам отзыв', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем  в задании task_name №task_id task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ], $locale),
            Notification::SEND_REVIEW_PERFORMER => __('О вас оставлен новый отзыв', [], $locale) . " \"$notification->name_task\" №$notification->task_id",
            Notification::RESPONSE_TO_TASK_FOR_USER => __('performer откликнулся на задания task_name', [
                'performer' => $notification->performer?->name, 'task_name' => $notification->name_task
            ], $locale),
            Notification::CANCELLED_TASK => __('Ваше задание task_name №task_id было отменено', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::ADMIN_COMPLETE_TASK => __('3адание task_name №task_id было завершено администрацией', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::ADMIN_CANCEL_TASK => __('3адание task_name №task_id было отменено администрацией', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::NEW_PASSWORD => __('Чтобы не потерять доступ к вашему аккаунту, рекомендуем вам установить пароль. Сделать это можно в профиле, раздел "Настройки".', [], $locale),
            Notification::WALLET_BALANCE => __('USer.Uz предоставил вам бонус в размере default сумов.', [
                'default' => setting('admin.bonus')
            ], $locale),
            default => 'Description',
        };
    }

}
