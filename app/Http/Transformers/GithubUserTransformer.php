<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class GithubUserTransformer extends TransformerAbstract
{
    /**
     * @param array $githubUsers
     * @return array
     */
    public function transform(array $githubUser): array
    {
        return [
            'id' => $githubUser['id'],
            'name' => $githubUser['name'],
            'login' => $githubUser['login'],
            'company' => $githubUser['company'],
            'number_of_followers' => $githubUser['followers'],
            'number_of_repositories' => $githubUser['public_repos'],
            'average_number_of_followers' => $this->handleAverageNumberOfFollowers(
                $githubUser['followers'],
                $githubUser['public_repos']
            ),
            'links' => ['uri' => '/github/users/'.$githubUser['id']],
        ];
    }

    /**
     * @param int $numberOfFollowers
     * @param int $numberOfRepositories
     * @return float
     */
    private function handleAverageNumberOfFollowers(int $numberOfFollowers, int $numberOfRepositories): float
    {
        if ($numberOfRepositories === 0) {
            return 0.00;
        }

        return round((($numberOfFollowers / $numberOfRepositories) * 100), 2);
    }
}
