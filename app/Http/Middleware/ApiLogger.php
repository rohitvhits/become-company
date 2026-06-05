<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\ApiLog;

class ApiLogger
{
    public function handle(Request $request, Closure $next)
    {
        $requestData = $request->all();

        $response = $next($request);
        try {
            ApiLog::create([
                'api_name'   => $request->path(),
                'url'        => $request->fullUrl(),
                'method'     => $request->method(),
                'request'    => json_encode($requestData),
                'response'   => $response->getContent(),
                'status_code'=> $response->status(),
                'status'     => $response->status() >= 200 && $response->status() < 300 ? 'success' : 'failure',
            ]);
        } catch (\Exception $e) {
            \Log::error('API Log Failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }
}