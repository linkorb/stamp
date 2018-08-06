<?php

namespace Stamp\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Stamp\Loader\YamlProjectLoader;
use Stamp\Generator;
use RuntimeException;

class AnalyzeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('analyze')
            ->setDescription('Analyze a project directory')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                null
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFilename = $input->getOption('config');
        if (!$configFilename) {
            $configFilename = getcwd() . '/stamp.yml';
        }
        if (!file_exists($configFilename)) {
            throw new RuntimeException("File not found: " . $configFilename);
        }
        $output->writeLn("Using configuration file: " . $configFilename);

        $projectLoader = new YamlProjectLoader();
        $project = $projectLoader->loadFile($configFilename);
        $data = $project->getData();
        $output->writeLn(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}
