<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class OutputJson
{
    public function handle(Request $request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
