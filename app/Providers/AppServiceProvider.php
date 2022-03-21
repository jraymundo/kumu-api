<?php

namespace App\Providers;

use App\Gateways\GithubGatewayService;
use App\Gateways\Interfaces\GithubGatewayServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Psr7\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->bind();
    }

    private function bind()
    {
        $this->app->bind(GithubGatewayServiceInterface::class, function () {
            return new GithubGatewayService(Config::get('settings.github_base_url'));
        });
    }
}
