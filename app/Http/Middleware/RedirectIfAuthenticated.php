<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        foreach (($guards ?: [null]) as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::user()->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('karyawan.dashboard');
            }
        }
        return $next($request);
    }
}
