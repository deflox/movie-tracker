<?php

namespace App\Http\Middleware;

use App\Constants\ErrorCodeConstants;
use App\Constants\ErrorMessageConstants;
use App\UserMovie;
use App\Util\API;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware to check if the user has access to a given user movie.
 *
 * @package App\Http\Middleware
 */
class CheckMovieAccessRights
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
        if ($request->getMethod() === "GET") {
            $userMovieId = $request->route('id');
        }
        if ($request->getMethod() === "POST") {
            $userMovieId = $request->get('userMovieId');
        }

        $count = UserMovie::where('id', $userMovieId)
            ->where('user_id', Auth::id())
            ->count();

        if ($count === 0) return API::error(
            ErrorCodeConstants::NO_PERMISSIONS,
            ErrorMessageConstants::NO_PERMISSION
        );

        return $next($request);
    }
}
