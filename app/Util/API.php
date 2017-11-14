<?php

namespace App\Util;

/**
 * Util class for API responses.
 *
 * @package App\Util
 */
class API
{
    /**
     * Creates a general API response with errors set to
     * false.
     *
     * @param  $content
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response(Array $content = null)
    {
        $response['errors'] = false;
        if ($content !== null) $response['content'] = $content;

        return response()->json($response);
    }

    /**
     * Creates a error API response with errors set to
     * true.
     *
     * @param  $code
     * @param  $errorMessage
     * @param  $errorMessages
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($code, $errorMessage = null, $errorMessages = null)
    {
        $response['errors'] = true;
        $response['errorCode'] = $code;
        if ($errorMessage !== null) $response['errorMessage'] = $errorMessage;
        if ($errorMessages !== null) $response['errorMessages'] = $errorMessages;

        return response()->json($response);
    }
}