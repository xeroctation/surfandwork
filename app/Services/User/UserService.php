<?php

namespace App\Services\User;

use App\Models\Task;
use App\Services\CustomService;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\{Auth, Cache, Hash, Mail};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserService
{

    /**
     * Verify number when creating task
     * @param $for_ver_func
     * @param $user
     * @param $sms_otp
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyProfile($for_ver_func, $user, $sms_otp): RedirectResponse
    {
        /** @var Task $task */
        $task = Task::select('phone')->find($for_ver_func);

        if ((int)$sms_otp === (int)$user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                if ($task->phone === null && $user->phone_number !== $task->phone && (int)$user->is_phone_number_verified === 0) {
                    $user->update(['is_phone_number_verified' => 0]);
                } else {
                    $user->update(['is_phone_number_verified' => 1]);
                    $user->phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
                    $user->save();
                }
                if ($task->phone === null) {
                    Task::findOrFail($for_ver_func)->update([
                        'status' => 1, 'user_id' => $user->id, 'phone' => (new CustomService)->correctPhoneNumber($user->phone_number)
                    ]);
                } else {
                    Task::findOrFail($for_ver_func)->update(['status' => Task::STATUS_OPEN, 'user_id' => $user->id,]);
                }
                auth()->login($user);

                //send notification
//                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $for_ver_func);
            }

            auth()->logout();
            return back()->with('expired_message', __('Срок действия номера истек'));
        }

        auth()->logout();
        return back()->with('incorrect_message', __('Ваш номер не подтвержден'));
    }



}
