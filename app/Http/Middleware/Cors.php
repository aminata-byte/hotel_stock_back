<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'http://localhost:5173', // Vite dev server
            'http://127.0.0.1:5173', // Vite dev server
            'http://hotel-frontend.local',
            'https://hotel-frontend.local',
            'http://hotel-frontend.test',
            'https://hotel-frontend.test',
        ];

        $origin = $request->header('Origin');

        $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : null;

        // Allow hotel-frontend-*.vercel.app domains
        if (!$allowedOrigin && preg_match('/^https:\/\/hotel-frontend-[a-zA-Z0-9-]+\.vercel\.app$/', $origin)) {
            $allowedOrigin = $origin;
        }

        $response = $next($request);

        if ($allowedOrigin) {
            $response->header('Access-Control-Allow-Origin', $allowedOrigin);
        }

        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
                ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-CSRF-TOKEN, Authorization, Accept, Origin')
                ->header('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
