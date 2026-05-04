<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LastModifiedHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response && $response->isOk()) {
            $lastModified = now()->toRfc7231String(); // or fetch real update time

            $response->headers->set('Last-Modified', $lastModified);
        }

        return $response;
    }
}

