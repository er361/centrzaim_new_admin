<?php

namespace App\Http\Controllers\Auth;

use App\Actions\ConfirmSmsCodeAction;
use App\Actions\DeactivateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmCodeRequest;
use App\Http\Requests\SendSmsRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use Illuminate\Support\Str;

class SmsLoginController extends Controller
{
    public function index()
    {
        return view('auth.sms.sms-login');
    }

    public function confirmPage(string $phone)
    {
        return view('auth.sms.sms-confirm', ['phone' => $phone]);
    }


    public function sendCode(SendSmsRequest $request, ActivationServiceInterface $activationService)
    {
        $phone = $request->input('phone');

        if (env('APP_ENV') === 'local') {
            $user = User::whereRoleId(Role::ID_USER)->first();
        } else {
            $user = User::where('mphone', $phone)->first();
        }


        if (!$user) {
            return redirect()->back()->withErrors([
                'Пользователь с таким номером телефона не найден'
            ]);
        }

        try {
            $activationService->sendCode($user);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => Str::limit($e->getTraceAsString(), 1000),
            ]);
            return redirect()->back()->withErrors([
                'Ошибка при отправке СМС',
            ]);
        }

        return redirect()->route('sms.confirm-page', ['phone' => $phone]);
    }

    public function confirm(ConfirmCodeRequest $request, ConfirmSmsCodeAction $action)
    {
        $phone = $request->input('phone');
        if (env('APP_ENV') === 'local') {
            $user = User::whereRoleId(Role::ID_USER)->first();
            \Auth::login($user);
            return redirect()->route('account.dashboard');
        } else {
            $user = User::where('mphone', $phone)->first();
        }

        if (!$user) {
            return redirect()->back()->withErrors([
                'Пользователь с таким номером телефона не найден'
            ]);
        }

        $isConfirmed = $action->run($user, $request->input('code'));
        if (!$isConfirmed) {
            return back()->withErrors([
                'Неверный код из СМС'
            ]);
        }
        \Auth::login($user);


        return redirect()->route('account.dashboard');
    }
}
