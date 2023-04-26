<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\User\LoginService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.signin');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loginPost(UserLoginRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('email', $data['email'])
            ->orWhere('phone_number', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            Alert::error(__('Пароль неверен'));
            return back();
        }
        if (!$user->isActive()) {
            Alert::error(__('Аккаунт отключен'));
            return back();
        }

        auth()->login($user);
        if (!$user->is_email_verified)
            VerificationService::send_verification(auth()->user());

        $request->session()->regenerate();

        if (session()->has('redirectTo')) {
            $url = session()->get('redirectTo');
            session()->forget('redirectTo');
            return redirect($url);
        }
        return redirect()->route('home');

    }

    public function send_email_verification(): RedirectResponse
    {
        VerificationService::send_verification(auth()->user());
        Alert::info(__('Ваша ссылка для подтверждения успешно отправлена!'));
        return redirect()->route('home');
    }

    public function verifyAccount(User $user, $hash): RedirectResponse
    {
        LoginService::verifyColum($user, $hash);
        auth()->login($user);
        Alert::success(__('Поздравляю'), __('Ваш адрес электронной почты успешно подтвержден'));
        return redirect()->route('home');

    }


    public function customRegister(UserRegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->get('password'));
        unset($data['password_confirmation']);
        /** @var User $user */
        $user = User::query()->create($data);
        $user->phone_number = $data['phone_number'] . '_' . $user->id;
        $user->save();
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus');
        $wallBal->user_id = $user->id;
        $wallBal->save();

        auth()->login($user);

        VerificationService::send_verification(auth()->user());

//        return redirect()->route('profile.profileData');
        return redirect()->route('home');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
