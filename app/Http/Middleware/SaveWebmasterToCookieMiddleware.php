<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class SaveWebmasterToCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Имя параметра, который нужно проверить
        $paramName = 'wmid';

        // Проверяем, есть ли параметр в запросе
        if ($request->has($paramName)) {
            $value = $request->input($paramName);

            // Устанавливаем куку в ответ
            $response = $next($request);
            $response->headers->setCookie(
                new Cookie('webmaster_id', $value, now()->addDays(30)) // Кука действует 30 дней
            );

            return $response;
        }

        return $next($request);
    }
}
