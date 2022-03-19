<?php

namespace App\Gateways;

use App\Gateways\Interfaces\GithubGatewayServiceInterface;
use Exception;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundation;

class GithubGatewayService implements GithubGatewayServiceInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * GithubGatewayService constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $user
     * @return Request
     */
    public function getUserInfo(string $user): Request
    {
        return new Request(Httpfoundation::METHOD_GET, $this->baseUrl.$user);
    }

    private function test()
    {
        return '{
  "login": "jraymundo",
  "id": 4113927,
  "node_id": "MDQ6VXNlcjQxMTM5Mjc=",
  "avatar_url": "https://avatars.githubusercontent.com/u/4113927?v=4",
  "gravatar_id": "",
  "url": "https://api.github.com/users/jraymundo",
  "html_url": "https://github.com/jraymundo",
  "followers_url": "https://api.github.com/users/jraymundo/followers",
  "following_url": "https://api.github.com/users/jraymundo/following{/other_user}",
  "gists_url": "https://api.github.com/users/jraymundo/gists{/gist_id}",
  "starred_url": "https://api.github.com/users/jraymundo/starred{/owner}{/repo}",
  "subscriptions_url": "https://api.github.com/users/jraymundo/subscriptions",
  "organizations_url": "https://api.github.com/users/jraymundo/orgs",
  "repos_url": "https://api.github.com/users/jraymundo/repos",
  "events_url": "https://api.github.com/users/jraymundo/events{/privacy}",
  "received_events_url": "https://api.github.com/users/jraymundo/received_events",
  "type": "User",
  "site_admin": false,
  "name": "JL",
  "company": "CodeBrew Inc",
  "blog": "",
  "location": null,
  "email": null,
  "hireable": null,
  "bio": "Ang pogiiiiiiiiiiii kooooooooooooooooooooooo",
  "twitter_username": null,
  "public_repos": 0,
  "public_gists": 0,
  "followers": 0,
  "following": 0,
  "created_at": "2013-04-10T10:09:04Z",
  "updated_at": "2022-03-15T13:31:05Z"
}';
    }
}
