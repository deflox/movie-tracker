<?php

namespace App\Http\Middleware;

use App\Constants\ErrorCodeConstants;
use App\Util\API;
use Closure;

/**
 * Middleware for checking if the request is a valid ajax request.
 *
 * @package App\Http\Middleware
 */
class CheckAjaxRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->ajax()) {
            return API::error(ErrorCodeConstants::INVALID_REQUEST_TYPE);
        }

        return $next($request);
    }
}
