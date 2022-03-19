<?php

namespace App\Exceptions;

use App\Http\Responses\ErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

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
     * @var ErrorResponse
     */
    private $errorResponse;

    /**
     * @var BaseHandler
     */
    private $baseHandler;

    /**
     * Handler constructor.
     * @param  ErrorResponse  $errorResponse
     * @param  BaseHandler  $baseHandler
     */
    public function __construct(ErrorResponse $errorResponse, BaseHandler $baseHandler)
    {
        $this->errorResponse = $errorResponse;
        $this->baseHandler = $baseHandler;
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * @param  Request  $request
     * @param  Throwable  $throwable
     * @return ErrorResponse|Response
     */
    public function render($request, Throwable $throwable)
    {
        return $this->baseHandler->render($request, $throwable);
    }
}
