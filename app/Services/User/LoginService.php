<?php

namespace App\Services\User;

use Carbon\Carbon;
use Exception;

class LoginService
{
    /**
     * Email verification
     * @param $user
     * @param $hash
     * @return bool
     * @throws Exception
     */
    public static function verifyColum($user, $hash): bool
    {
        $result = false;

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($hash === $user->verify_code || $hash === setting('admin.CONFIRM_CODE',123456)) {
                $user->is_email_verified = 1;
                $user->save();
                $result = true;
            }
        } else {
            abort(419);
        }
        return $result;
    }

}
