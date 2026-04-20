<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'superadmin') {
            return $next($request);
        }
        // Jika bukan admin, tendang kembali ke dashboard dengan pesan error
        return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki hak akses Super Admin.');
    }
}
