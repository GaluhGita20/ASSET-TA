<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class api_key
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasHeader('x-api-key') && $request->header('x-api-key') == env('API_KEY')) {
            return $next($request);
            // return 'tes';
        }

        return response()->json([
            'status' => 'error',
            'message' =>  $request->header('x-api-key'),
            // 'message' => 'Akses ditolak. API key tidak sesuai.',
        ], Response::HTTP_FORBIDDEN);
    }
    //     return $next($request);
    // }
}
