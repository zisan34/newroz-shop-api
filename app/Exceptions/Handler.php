<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
    public function render($request, $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            // Code here ...
            if($request->expectsJson()){
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => [$exception->getMessage()]
                ], 403);                
            }
        }

        return parent::render($request, $exception);
    }

    protected function invalidJson($request, \Illuminate\Validation\ValidationException $exception)
    {
        $errors = [];
        foreach ($exception->errors() as $key => $error) {
            $errors[] = $error[0];
        }
        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $errors,
        ], $exception->status);
    }
}
