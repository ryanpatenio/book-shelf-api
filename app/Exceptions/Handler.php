<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Customize the response for unauthenticated users.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Check for AuthenticationException (when token is invalid or missing)
        if ($exception instanceof AuthenticationException) {
            return response()->json(['message' => 'Unauthenticated. Please provide a valid token.'], 401);
        }
    
        // Handle database errors for database queries
        if ($exception instanceof \Illuminate\Database\QueryException) {
            // In production, avoid revealing sensitive database error information
            if (app()->environment('local')) {
                // In local environment, log the error message and show detailed error
                return json_message(EXIT_BE_ERROR, 'Database error', ['error' => $exception->getMessage()]);
            } else {
                // In production, show a generic database error message to the user
                return json_message(EXIT_BE_ERROR, 'Database error, please try again later.', ['error' => 'An error occurred while processing the request.']);
            }
        }
    

        // Default error response for any other exceptions
        return parent::render($request, $exception);
    }
    
}
