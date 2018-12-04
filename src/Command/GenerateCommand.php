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

class GenerateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('generate')
            ->setDescription('Generate files from stamp.yml')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'Configuration file to use',
                getcwd() . '/stamp.yml'
            )
            ->addOption(
                'json',
                'j',
                InputOption::VALUE_REQUIRED,
                'Json file to use (used by default - metaculous.json)',
                getcwd() . '/metaculous.json'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFilename = $input->getOption('config');

        if (!file_exists($configFilename)) {
            throw new RuntimeException("File not found: " . $configFilename);
        }
        $output->writeLn("Using configuration file: " . $configFilename);
        $projectLoader = new YamlProjectLoader();
        $project = $projectLoader->loadFile($configFilename);

        $jsonFilename = $input->getOption('json');
        $json = [];
        if (!file_exists($jsonFilename)) {
            throw new RuntimeException("Json file not found: " . $jsonFilename);
        }
        $jsonData = json_decode(file_get_contents($jsonFilename), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Error parsing json file: " . $jsonFilename);
        }
        $project->setAnalyzedData($jsonData);

        $generator = new Generator($project);
        $generator->generate();
    }
}
