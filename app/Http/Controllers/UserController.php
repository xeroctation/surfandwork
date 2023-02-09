<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends Controller
{

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


}
