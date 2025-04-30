<?php

namespace App\Http\Controllers\Front\Miazaim;

use App\Actions\ConfirmSmsCodeAction;
use App\Actions\DeactivateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\ConfirmCodeRequest;
use App\Http\Requests\SendSmsRequest;
use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CancelSubscriptionController extends Controller
{
    //
    public function index()
    {
        return view('cancel-subscription.index');
    }

    public function confirmPage(string $phone)
    {
        return view('cancel-subscription.confirm', ['phone' => $phone]);
    }

    public function notFound(string $phone)
    {
        return view('cancel-subscription.not-found', ['phone' => $phone]);
    }

    public function success()
    {
        return view('cancel-subscription.success');
    }

    public function sendCode(SendSmsRequest $request, ActivationServiceInterface $activationService)
    {
        $phone = $request->input('phone');
        $user = User::where('mphone', $phone)->first();

        if(!$user){
            return redirect()->route('front.unsubscribe.not.found', ['phone' => $phone]);
        }
        try {
            $activationService->sendCode($user);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => Str::limit($e->getTraceAsString(), 1000),
            ]);
            return redirect()->route('front.unsubscribe.confirm.page',['phone' => $phone])->withErrors([
                'Ошибка при отправке СМС',
            ]);
        }
        //send sms and render confirm page
        return redirect()->route('front.unsubscribe.confirm.page', ['phone' => $phone]);
    }

    public function confirm(ConfirmCodeRequest $request, ConfirmSmsCodeAction $action, DeactivateUserAction $deactivateUserAction)
    {
        $phone = $request->input('phone');
        $user = User::where('mphone', $phone)->first();

        if(!$user){
            return redirect()->route('front.unsubscribe.not.found', ['phone' => $phone]);
        }

        $isConfirmed = $action->run($user, $request->input('code'));
        if(!$isConfirmed) {
            return back()->withErrors([
                'Неверный код из СМС'
            ]);
        }
        \Auth::login($user);

        $deactivateUserAction->run($user);

        return redirect()->route('front.unsubscribe.success');
    }
}
