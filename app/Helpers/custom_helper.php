<?php

if (!function_exists('json_message')) {
    /**
     * Return a formatted JSON response.
     *
     * @param string $message
     * @param int $status
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    function json_message($code,$message, $data = [])
    {
        return response()->json([
            'code'    => $code,
            'message' => $message,          
            'data' => $data,
        ], 200);
    }

    if (!defined('EXIT_SUCCESS')) {
        define('EXIT_SUCCESS', 0);
    }

    if (!defined('EXIT_BE_ERROR')) {
        define('EXIT_BE_ERROR', 1);
    }
    if (!defined('EXIT_FORM_NULL')) {
        define('EXIT_FORM_NULL', 3);
    }
}