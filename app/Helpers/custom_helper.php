<?php

/**
 * constant
 */

use Illuminate\Database\Eloquent\ModelNotFoundException;

if (!defined('EXIT_SUCCESS')) {
    define('EXIT_SUCCESS', 0);
}

if (!defined('EXIT_BE_ERROR')) {
    define('EXIT_BE_ERROR', 1);
}
if (!defined('EXIT_FORM_NULL')) {
    define('EXIT_FORM_NULL', 3);
}

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

    
}


if (!function_exists('handleException')) {
    /**
     * Handle exceptions by logging and returning a formatted error response.
     *
     * @param \Throwable $exception
     * @param string $customMessage Custom error message to return
     * @param int $statusCode HTTP status code (default: 500)
     * @param int $exitCode Custom exit code for the application
     * @return \Illuminate\Http\JsonResponse
     */
    function handleException(\Throwable $exception, $defaultMessage = 'An error occurred.')
    {
        // Log the exception details
        \Log::error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);

        // Handle specific exceptions, for example ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 404,
                'message' => 'Resource not found',
            ], 404);
        }

        // Return a generic error response
        return response()->json([
            'status' => 500,
            'message' => $defaultMessage,
            'error' => $exception->getMessage(),
        ], 500);
    }
}
