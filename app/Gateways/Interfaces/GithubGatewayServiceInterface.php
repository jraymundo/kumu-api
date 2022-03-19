<?php

namespace App\Gateways\Interfaces;

use GuzzleHttp\Psr7\Request;

interface GithubGatewayServiceInterface
{
    /**
     * Get github information by username
     *
     * @param string $gitHubUserNames
     * @return Request
     */
    public function getUserInfo(string $gitHubUserNames): Request;
}
