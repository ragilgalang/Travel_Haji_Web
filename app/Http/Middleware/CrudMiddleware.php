<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrudMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya admin atau manager yang bisa akses route CRUD
        if ($user && in_array($user->role, ['admin', 'manager'])) {
            return $next($request);
        }

        abort(403, 'Akses CRUD ditolak, hanya untuk admin dan manager.');
    }
}
