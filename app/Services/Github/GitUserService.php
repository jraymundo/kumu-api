<?php

namespace App\Services\Github;

use App\Gateways\Interfaces\GithubGatewayServiceInterface;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Collection;
use Redis;

class GitUserService
{
    /**
     * @var GithubGatewayServiceInterface
     */
    private GithubGatewayServiceInterface $githubGatewayService;

    /**
     * @var Redis
     */
    private Redis $redis;

    /**
     * GitUserService constructor.
     * @param GithubGatewayServiceInterface $githubGatewayService
     */
    public function __construct(GithubGatewayServiceInterface $githubGatewayService, Redis $redis)
    {
        $this->githubGatewayService = $githubGatewayService;
        $this->redis = $redis;

        $redis->connect('localhost', 6379);
    }

    /**
     * @param array $gitHubUserNames
     * @return Collection
     */
    public function handleUsers(array $gitHubUserNames): Collection
    {
        list($redisUserList, $gitHubUserNames) = $this->collectFromRedis($gitHubUserNames);

        $userList = array_merge($redisUserList, $this->collectFromGithub($gitHubUserNames));

        if (!empty($userList)) {
            ksort($userList);

            return new Collection(array_values($userList));
        }

        return new Collection([]);
    }

    /**
     * @param $gitHubUserNames
     * @return array
     */
    private function collectFromRedis($gitHubUserNames)
    {
        $redisUserList = [];

        foreach ($gitHubUserNames as $key => $user) {

            // if user exist in Redis then we skip
            if ($this->redis->get(strtolower($user))) {

                // decode the stringified json
                $user = json_decode($this->redis->get(strtolower($user)), true);

                //Collate users and trim name so we can sort accordingly
                $redisUserList[$this->nameTrimmer($user)] = $user;

                unset($gitHubUserNames[$key]);
            }
        }

        return [$redisUserList, $gitHubUserNames];
    }

    /**
     * @param array $gitHubUserNames
     * @return array
     */
    private function collectFromGithub(array $gitHubUserNames): array
    {
        $githubList = [];

        if (!empty($gitHubUserNames)) {

            $promises = function () use ($gitHubUserNames) {
                foreach ($gitHubUserNames as $user) {
                    yield $this->githubGatewayService->getUserInfo($user);
                }
            };

            $pool = new Pool(new Client(), $promises(), [
                'concurrency' => 10,
                'fulfilled' => function ($response, $index) use (&$githubList) {

                    //Decode result
                    $user = json_decode($response->getBody(), true);

                    //Store value to redis
                    $this->redis->set(strtolower($user['login']), json_encode($user), 120);

                    //Collate users
                    $githubList[$this->nameTrimmer($user)] = $user;
                },
                'rejected' => function ($reason, $index) {
                    //do a simple Logging perhaps
                },
            ]);

            $pool->promise()->wait();
        }

        return $githubList;
    }

    /**
     * @param array $user
     * @return string
     */
    private function nameTrimmer(array $user): string
    {
        return strtolower((str_replace(' ', '', $user['name'] ?? $user['login'])));
    }
}
