<?php

namespace App\Exceptions;

use App\Exceptions\RedirectCallBack\RedirectCallBackException;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseHandler
{
    /**
     * @var ApiResponse
     */
    private $response;

    /**
     * BaseHandler constructor.
     *
     * @param ApiResponse $response
     */
    public function __construct(ApiResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     *
     * @return ErrorResponse|Response
     */
    public function render(Request $request, Throwable $exception)
    {
        $params = $request->all();

        if ($exception instanceof UnauthorizedException) {
            return $this->response->error()->unAuthorized($exception->getMessage());
        }

        if ($exception instanceof NotFoundException) {
            return $this->response->error()->httpNotFound($exception->getMessage());
        }

        return $this->response->error()->internalServer($exception->getMessage());
    }

}
