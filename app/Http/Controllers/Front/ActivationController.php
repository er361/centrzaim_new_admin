<?php


namespace App\Http\Controllers\Front;


use App\Events\UserActivated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use App\Services\LinkService\LinkCreatorService;
use App\Services\SettingsService\SettingsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    /** @var string Сообщение об ошибке */
    protected const ERROR_MESSAGE = 'Неверный код';

    /**
     * @param Request $request
     * @param ActivationServiceInterface $activationService
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request, ActivationServiceInterface $activationService)
    {
        return view('front.centrzaim.account.activation');
    }

    /**
     * Формирует ссылку на Telegram-бота и открывает её
     * 
     * @return RedirectResponse
     */
    public function telegramActivation()
    {
        /** @var User $user */
        $user = Auth::user();
        $linkCreatorService = new LinkCreatorService([]);
        // Формирование ссылки на Telegram-бота с использованием LinkCreatorService
        $telegramLink = $linkCreatorService->getTelegramBotLink($user);

        
        return redirect()->away($telegramLink);
    }
    
    /**
     * Показывает страницу ввода кода из телеграм
     * 
     * @return Application|Factory|View
     */
    public function telegramActivationPage()
    {
        /** @var User $user */
        $user = Auth::user();
        
        return view('front.centrzaim.account.telegram_activation', [
            'phone' => $user->mphone
        ]);
    }

    /**
     * Перенаправление на страницу активации через СМС
     * 
     * @return Application|Factory|View
     */
    public function smsActivation(Request $request, ActivationServiceInterface $activationService)
    {

        /** @var User $user */
        $user = Auth::user();

        if (!SettingsService::isPhoneVerificationEnabled()) {
            $user->update([
                'is_active' => true,
            ]);
            event(new UserActivated($user));
            return redirect()->route('account.fill.index');
        }

        try {
            $activationService->sendCode($user);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => Str::limit($e->getTraceAsString(), 1000),
            ]);
            return view('front.centrzaim.account.sms_activation')->withErrors([
                self::ERROR_MESSAGE,
            ]);
        }

        return view('front.centrzaim.account.sms_activation', [
            'phone' => $user->mphone,
        ]);
    }

    /**
     * Переотправка кода.
     *
     * @param ActivationServiceInterface $activationService
     *
     * @return RedirectResponse
     */
    public function resend(ActivationServiceInterface $activationService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $activationService->resendCode($user);
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => Str::limit($e->getTraceAsString(), 1000),
            ]);
            return redirect()->back()->withErrors([
                self::ERROR_MESSAGE,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Ввод кода активации.
     *
     * @param Request $request
     * @param ActivationServiceInterface $activationService
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function store(Request $request, ActivationServiceInterface $activationService)
    {
        /** @var User $user */
        $user = Auth::user();

        $isValid = $activationService->validateCode($user, $request->input('code'));

        if(config('sms_activation.mode') === 'local') {
            $isValid = true;
            $user->is_active = true;
            $user->save();
        }

        if ($isValid) {
            event(new UserActivated($user));
            return redirect()->route('account.fill.index');
        }

        return redirect()->back()->withErrors([
            self::ERROR_MESSAGE,
        ]);
    }

    public function validateCode(Request $request, ActivationServiceInterface $activationService)
    {
        /** @var User $user */
        $user = Auth::user();

        $isValid = $activationService->validateCode($user, $request->input('code'));
        if($isValid){
            return response()->json(['errors' => []], 200);
        }
        return response()->json(['errors' => [self::ERROR_MESSAGE]], 422);
    }
}