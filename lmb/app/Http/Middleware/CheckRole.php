<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole {
    public function handle(Request $request, Closure $next) {
        return Auth::check() && Auth::user()->role_id == 1 ? $next($request) : abort(403);
    }
}
