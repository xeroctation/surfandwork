<?php

namespace App\Services;

use App\Mail\VerificationEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    /**
     * @param $user
     * @param null $phone_number
     * @param null $email
     * @throws \Exception
     */
    public static function send_verification($user, $phone_number = null, $email = null): void
    {
        $message = sha1(time());
        $data = [
            'code' => $message,
            'user' => $user->id
        ];
        if ($email) {
            Mail::to($email)->send(new VerificationEmail($data, $user, $email));
        } else {
            Mail::to($user->email)->send(new VerificationEmail($data, $user, $email));
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }
}
