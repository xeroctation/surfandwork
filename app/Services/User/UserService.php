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

        if ((int)$sms_otp === (int)$user->verify_code) {
//            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if (1) {

                auth()->login($user);

                //send notification

                return redirect()->route('searchTask.task', $for_ver_func);
            }

            auth()->logout();
            return back()->with('expired_message', __('Срок действия номера истек'));
        }

        auth()->logout();
        return back()->with('incorrect_message', __('Ваш номер не подтвержден'));
    }



}
