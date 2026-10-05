<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        abort_unless(in_array($request->user()?->role, $roles), 403, 'Anda tidak punya akses ke halaman ini.');
        return $next($request);
    }
}
