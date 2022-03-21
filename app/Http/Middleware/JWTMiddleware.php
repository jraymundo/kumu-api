<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class JWTMiddleware
{
    /**
     * @var ErrorResponse
     */
    private ErrorResponse $errorResponse;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * JWTMiddleware constructor.
     * @param ErrorResponse $errorResponse
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(ErrorResponse $errorResponse, UserRepositoryInterface $userRepository)
    {
        $this->errorResponse = $errorResponse;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $this->parseToken($request);

        $payload = $this->decodePayload($bearerToken);

        $user = $this->validateToken($bearerToken, $payload);

        $request->request->add(['user' => $user]);

        return $next($request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws UnauthorizedException
     */
    private function parseToken(Request $request): string
    {
        $bearerToken = $request->bearerToken();

        if (!$request->bearerToken()) {
            throw new UnauthorizedException('Token not found');
        }

        return $bearerToken;
    }

    /**
     * @param string $token
     * @return array
     * @throws UnauthorizedException
     */
    private function decodePayload(string $token): array
    {
        $parsedJwt = explode('.', $token);

        $payload = json_decode(
            base64_decode(
                str_pad(strtr($parsedJwt[1], '-_', '+/'), strlen($parsedJwt[1]) % 4, '=', STR_PAD_RIGHT)
            ),
            true);

        if (empty($payload)) {
            throw new UnauthorizedException('Invalid token payload');
        }

        return $payload;
    }

    /**
     * @param string $bearerToken
     * @param array $payload
     * @return User
     * @throws UnauthorizedException
     */
    private function validateToken(string $bearerToken, array $payload): User
    {
        $user = $this->userRepository->getOneByAttributes(['uuid' => $payload['uuid']]);

        if (!$user instanceof User) {
            throw new UnauthorizedException('Invalid Token User');
        }

        try {
            JWT::decode($bearerToken, new Key(Config::get('settings.api_secret'), 'HS256'));
        } catch (Exception $exception) {
            throw new UnauthorizedException($exception->getMessage());
        }

        unset($user->password);

        return $user;
    }
}
