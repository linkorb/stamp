<?php

namespace Stamp\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Stamp\Model\UserConfig;
use Github\Client;
use Github\Exception\RuntimeException as ApiError;

class GithubTokenCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('github-token')
            ->setDescription('Set personal access token for the command line')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'Token retrieved from https://github.com/settings/tokens (with `repo` scope selected)'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $token = $input->getArgument('token');

        if ($e = $this->testToken($token)) {
            $io->getErrorStyle()->error($e->getMessage());
        } else {
            (new UserConfig())->setGithubToken($token);
            $io->getErrorStyle()->success("Token has been set");
        }
    }

    private function testToken(string $token): ?\Exception {
        try {
            $client = new Client();
            $client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);
            $client->currentUser()->repositories();

            return null;
        } catch (ApiError $e) {
            return $e;
        }
    }
}
