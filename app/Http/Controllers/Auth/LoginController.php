<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected string $redirectTo = '/my/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => [
                'required_if:force_password,1',
            ],
        ]);
    }

    public function showLoginForm()
    {
        $previousUrl = url()->previous();
        $adminUrl = route('admin.index');

        $showPassword = Str::startsWith($previousUrl, $adminUrl)
            || request()->input('force_password') === '1';

        return view('auth.login', compact('showPassword'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (!$request->has('force_password')) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, User $user)
    {
        if ($user->role_id === Role::ID_USER) {
            return redirect($this->redirectPath());
        }

        return redirect()->route('admin.index');
    }

    /**
     * Custom logout method that allows both POST and GET requests
     * Overrides the logout method from AuthenticatesUsers trait
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
