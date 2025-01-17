<?php

/**
 * constant && custom helpers with Handle Exceptions
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
    // Only log detailed trace information in local or development environment
    if (app()->environment('local')) {
        \Log::error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
    } else {
        \Log::error($exception->getMessage());  // Log only the message in production
    }

    // Handle specific exceptions, for example ModelNotFoundException
    if ($exception instanceof ModelNotFoundException) {
        // Log the specific exception but don't expose it to the user
        return response()->json([
            'status' => 404,
            'message' => 'Resource not found',
        ], 404);
    }

    // Handle other exceptions and return a generic error message
    // Do not expose the exception details in production
    $errorMessage = app()->environment('local') ? $exception->getMessage() : 'An unexpected error occurred.';

    // Return a generic error response
    return response()->json([
        'status' => 500,
        'message' => $defaultMessage,
        'error' => $errorMessage,  // In production, only send a generic message
    ], 500);
}

    
}
