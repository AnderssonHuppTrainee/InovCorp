<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class GlobalRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 100, int $decayMinutes = 1): Response
    {
        try {
            $key = $this->resolveRequestSignature($request);

            // Verificar rate limit com tratamento de erro
            try {
                if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                    $seconds = RateLimiter::availableIn($key);

                    return response()->json([
                        'error' => 'Too many requests',
                        'retry_after' => $seconds,
                        'message' => "Rate limit exceeded. Try again in {$seconds} seconds."
                    ], 429);
                }

                RateLimiter::hit($key, $decayMinutes * 60);
            } catch (\Exception $cacheError) {
                // Se houver erro no cache, logar mas continuar
                \Log::warning('Cache error in GlobalRateLimit, continuing without rate limiting', [
                    'error' => $cacheError->getMessage(),
                    'url' => $request->url(),
                    'user_id' => $request->user()?->id
                ]);
            }

            $response = $next($request);

            // Adicionar headers de rate limit com tratamento de erro
            try {
                $response->headers->set('X-RateLimit-Limit', $maxAttempts);
                $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
                $response->headers->set('X-RateLimit-Reset', now()->addSeconds(RateLimiter::availableIn($key))->timestamp);
            } catch (\Exception $headerError) {
                // Se houver erro nos headers, logar mas continuar
                \Log::warning('Error setting rate limit headers', [
                    'error' => $headerError->getMessage()
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            \Log::error('Error in GlobalRateLimit middleware', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->url(),
                'user_id' => $request->user()?->id
            ]);

            // Em caso de erro crítico, continuar sem rate limiting
            return $next($request);
        }
    }

    /**
     * Resolve request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        try {
            $user = $request->user();
            $ip = $request->ip();

            if ($user) {
                return 'user:' . $user->id . ':' . $ip;
            }

            return 'ip:' . $ip;
        } catch (\Exception $e) {
            \Log::error('Error in resolveRequestSignature', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->url()
            ]);
            throw $e;
        }
    }
}
