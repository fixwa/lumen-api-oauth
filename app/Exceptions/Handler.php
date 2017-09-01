<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if(env('APP_DEBUG')){
            return parent::render($request, $e);
        }

        if($e instanceof NotFoundHttpException){
            Log::error("Not found: " . $e->getMessage());
            return response()->json(['message' => 'Bad Request', 'code' => 400], 400);
        }

        if($e instanceof MethodNotAllowedHttpException){
            Log::error("Method not allowed: " . $e->getMessage());
            return response()->json(['message' => 'Not Found', 'code' => 404], 404);
        }

        Log::error("Unexpected Error: " . $e->getMessage());
        return response()->json(['message' => 'Unexpected Error', 'code' => 500], 500);
    }
}
