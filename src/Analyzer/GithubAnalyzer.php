<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Stamp\Model\UserConfig;
use Github\Client;

class GithubAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        if ($origin = $project->git('remote get-url origin')) {
            preg_match("/^git@github.com:(.*)\/(.*).git$/", $origin, $out) || preg_match("/^https?:\/\/github.com\/(.*)\/(.*).git$/", $origin, $out);

            if (count($out) === 3) {
                $userConfig = new UserConfig();

                $client = new Client();
                $client->authenticate($userConfig->getGithubToken(), null, Client::AUTH_HTTP_TOKEN);

                return ['github' => [
                    'repository'   => $client->api('repo')->show($out[1], $out[2]),
                    'contributors' => $client->api('repo')->contributors($out[1], $out[2])
                ]];
            }
        }

        return null;
    }
}
