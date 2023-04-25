<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyProfileRequest;
use App\Models\User;
use App\Models\Task;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends Controller
{

    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function code()
    {
        return view('auth.register_code');
    }

    public function signup()
    {
        return view('auth.signup');
    }

    public function reset()
    {
        return view('auth.reset');
    }

    public function confirm()
    {
        return view('auth.confirm');
    }

    public function verifyProfile(VerifyProfileRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $for_ver_func = $data['for_ver_func'];
        $sms_otp = '123';
        return $this->service->verifyProfile($for_ver_func, $user, $sms_otp);
    }
}
