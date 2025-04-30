<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivationService\ActivationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResendSmsController extends Controller
{
    public function __invoke(string $phone, ActivationServiceInterface $activationService): RedirectResponse
    {
        $user = User::where('mphone', $phone)->first();
        if (!$user) {
            return redirect()->back()->withErrors([
                'Пользователь не найден',
            ]);
        }

        try {
            $activationService->resendCode($user);
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => Str::limit($e->getTraceAsString(), 1000),
            ]);
            return redirect()->back()->withErrors([
                'Ошибка при отправке СМС',
            ]);
        }

        return redirect()->back();
    }
}
