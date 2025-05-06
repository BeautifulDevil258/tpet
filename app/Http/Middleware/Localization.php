<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy ngôn ngữ từ header Accept-Language
        $locale = $request->getPreferredLanguage(['en', 'vi']); // 'en' và 'vi' là các ngôn ngữ bạn hỗ trợ

        // Lưu ngôn ngữ vào session và set ngôn ngữ cho ứng dụng
        Session::put('locale', $locale);
        App::setLocale($locale);

        return $next($request);
    }
}
