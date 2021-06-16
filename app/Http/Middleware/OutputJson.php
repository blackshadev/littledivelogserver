<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OutputJson
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if (is_array($response)) {
            return response()->json(
                $response,
                200,
                [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Charset' => 'utf-8'
                ],
                JSON_UNESCAPED_UNICODE
            );
        }

        return $response;
    }
}
