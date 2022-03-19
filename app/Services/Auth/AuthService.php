<?php

namespace App\Services\Auth;

use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    /**
     * AuthService constructor.
     * @param UserRepositoryInterface $userRepository
     * @param Hasher $hasher
     */
    public function __construct(UserRepositoryInterface $userRepository, Hasher $hasher)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * Generates JWT
     *
     * @param array $parameters
     * @return string
     * @throws UnauthorizedException
     */
    public function signToken(array $parameters)
    {
        $user = $this->validateUser($parameters);

        return $this->generateJWT($user);
    }

    /**
     * Creates new user and generate token for access
     *
     * @param array $parameters
     * @return string
     */
    public function register(array $parameters): string
    {
        $user = $this->userRepository->create([
            'uuid' => Str::random(20),
            'username' => $parameters['username'],
            'password' => $this->hasher->make($parameters['password']),
        ]);

        return $this->generateJWT($user);
    }

    /**
     * @param array $parameters
     * @return User
     * @throws UnauthorizedException
     */
    private function validateUser(array $parameters): User
    {
        $user = $this->userRepository->getOneByAttributes(['username' => $parameters['username']]);

        /**
         * Is User Exist
         */
        if (!$user instanceof User) {
            throw new UnauthorizedException('Invalid credentials');
        }

        /**
         * Is Password valid
         */
        if (!$this->hasher->check($parameters['password'], $user->password)) {
            throw new UnauthorizedException('Invalid credentials or account might have been disabled');
        }

        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateJWT(User $user): string
    {
        $payload = array(
            'iat' => time(),
            'exp' => time() + 20800,
            'uuid' => $user->uuid,
            'sub' => Config::get('settings.api_key'),
        );

        return JWT::encode($payload, Config::get('settings.api_secret'), 'HS256');
    }
}
