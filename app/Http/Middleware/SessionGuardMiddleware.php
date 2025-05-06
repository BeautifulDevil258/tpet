<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class SessionGuardMiddleware
{
    public function handle($request, Closure $next)
    {
        // Lấy guard hiện tại
        $guard = Auth::getDefaultDriver();

        // Đặt cookie riêng cho từng guard
        if ($guard === 'web') {
            Config::set('session.cookie', 'web_session');
        } elseif ($guard === 'admin') {
            Config::set('session.cookie', 'admin_session');
        }

        return $next($request);
    }
}
