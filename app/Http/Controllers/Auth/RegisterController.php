<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistrationStep1DoneEvent;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\RegisterValidatorService;
use App\Services\Auth\RegisterService;
use App\Services\OffersChecker\OfferProcessingService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use RegistersUsers {
        register as registerTrait;
    }

    protected string $redirectTo = '/my/activation';
    protected RegisterValidatorService $validatorService;
    protected RegisterService $registerService;

    public function __construct(RegisterValidatorService $validatorService,
                                RegisterService          $registerService,
                                OfferProcessingService   $offerProcessingService
    )
    {
        $this->middleware('guest');
        $this->validatorService = $validatorService;
        $this->registerService = $registerService;
        $this->offerProcessingService = $offerProcessingService;
    }

    public function showRegistrationForm(Request $request)
    {
        $settings = setting()->all();
        $shouldRedirectPp = \Illuminate\Support\Arr::get($settings, 'should_redirect_to_register_page_from_sources', '0');
        $shouldRedirectPp = $shouldRedirectPp && \Illuminate\Support\Arr::get($settings, 'is_redirect_enabled', '0');
        
        // Get amount and days parameters from the request to pass to the form
        $amount = $request->query('amount');
        $days = $request->query('days');
        
        return view('auth.register', compact('shouldRedirectPp', 'amount', 'days'));
    }

    public function register(Request $request)
    {
        Log::debug('Регистрируем нового пользователя.', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'request' => $request->except(['password']),
            'url' => $request->url(),
            'method' => $request->method(),
        ]);

        $validator = $this->validatorService->validate($request->all());

        //из за редиректа страницы регистрации, ошибки стираются, поэтому сохраняем их в сессию навсегда и удаляем в случае успешной регистрации
        if ($validator->fails()) {
            session()->put('_old_input', $request->all()); // Сохраняем данные формы вручную
            session()->put('errors', $validator->errors()); // Сохраняем ошибки вручную
            return redirect()->route('auth.register');
        } else {
            session()->forget('_old_input');
            session()->forget('errors');
        }

        $data = $validator->validated();
        
        // Get amount and days from form data instead of query parameters
        if ($request->has('amount')) {
            $data['credit_sum'] = $request->input('amount');
        }
        
        if ($request->has('days')) {
            $data['credit_days'] = $request->input('days');
        }

        $user = $this->registerService->createUser($data);

        Auth::login($user);

        event(new UserRegistrationStep1DoneEvent($user));

        $this->offerProcessingService->handle($user->name, $user->mphone);

        return redirect($this->redirectTo);

    }

    public function validateRegister(Request $request)
    {
        $validator = $this->validatorService->validate($request->all());
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        return response()->json(['errors' => []], 200);
    }

    protected function handleRegistrationErrors(ValidationException $e, Request $request)
    {
        // Обработка ошибок регистрации и логика, связанная с уникальными пользователями
        $isPhoneError = $e->validator->errors()->has('mphone');
        $isEmailError = $e->validator->errors()->has('email');

        if (!$isPhoneError && !$isEmailError) {
            throw $e;
        }

        $user = $isEmailError
            ? User::query()->where('email', $request->input('email'))->first()
            : User::query()->where('mphone', $this->registerService->convertPhone($request->input('mphone')))->latest()->first();

        if ($user === null || $user->role_id !== Role::ID_USER) {
            throw $e;
        }

        Auth::login($user);

        return redirect()->route('account.dashboard');
    }
}
