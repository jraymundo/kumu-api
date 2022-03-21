<?php

namespace App\Http\Controllers\Api;

use App\Http\Responses\FractalResponse;
use App\Services\Auth\AuthService;
use App\Services\Github\GitUserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class GithubController extends BaseApiController
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
     * @var GitUserService
     */
    private GitUserService $gitUserService;

    /**
     * GithubController constructor.
     * @param FractalResponse $response
     * @param GitUserService $gitUserService
     */
    public function __construct(FractalResponse $response, GitUserService $gitUserService)
    {
        $this->response = $response;
        $this->gitUserService = $gitUserService;
    }

    /**
     * @param Request $request
     * @return FractalResponse|HttpResponse
     */
    public function getUsers(Request $request)
    {
        $users = $this->gitUserService->handleUsers($request->input('users'));

        return $this->response->customCollection('github_users', $users, 'GithubUser');
    }
}
