<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        if (!Auth::check()) {
            return redirect()->route($ADMIN_ROUTE_NAME.'.login');
        }

        return $next($request);
    }
}
