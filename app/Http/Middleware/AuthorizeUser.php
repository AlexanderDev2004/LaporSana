<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    if (!Auth::check()) {
        return redirect('login')->withErrors(['msg' => 'Silakan login terlebih dahulu']);
    }

    // Change this line to use roles_id instead of roles
    $user_role = Auth::user()->roles_id;

    if (in_array($user_role, $roles)) {
        return $next($request);
    }

    abort(403, 'Kamu tidak punya akses ke halaman ini');
}
}
