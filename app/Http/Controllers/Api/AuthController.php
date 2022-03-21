<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnauthorizedException;
use App\Http\Responses\FractalResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends BaseApiController
{
    /**
     * @var FractalResponse
     */
    private FractalResponse $response;

    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * AuthController constructor.
     * @param FractalResponse $response
     * @param AuthService $authService
     */
    public function __construct(FractalResponse $response, AuthService $authService)
    {
        $this->response = $response;
        $this->authService = $authService;
    }

    /**
     * @param Request $request
     * @return FractalResponse|HttpResponse
     * @throws UnauthorizedException
     */
    public function login(Request $request)
    {
        $token = $this->authService->signToken($request->all());

        return $this->response->outputToJson(['token' => $token]);
    }

    /**
     * @param Request $request
     * @return FractalResponse|HttpResponse
     * @throws UnauthorizedException
     */
    public function register(Request $request)
    {
        $token = $this->authService->register($request->all());

        return $this->response->outputToJson(['token' => $token]);
    }
}
