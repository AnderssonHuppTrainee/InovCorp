<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugCSRF
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug CSRF para requisições de mensagens
        if ($request->is('rooms/*/messages') && $request->isMethod('POST')) {
            \Log::info('Debug CSRF - Requisição de mensagem:', [
                'url' => $request->url(),
                'method' => $request->method(),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'session_token' => $request->session()->token(),
                'user_id' => auth()->id(),
                'headers' => $request->headers->all(),
                'session_id' => $request->session()->getId(),
            ]);
        }

        return $next($request);
    }
}
