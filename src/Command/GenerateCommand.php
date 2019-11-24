<?php

namespace Stamp\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Yaml;
use Stamp\Model\Project;
use Loader\Loader;
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
            ->setDescription('Generate files from stamp.yaml')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'Configuration file to use',
                getcwd() . '/stamp.yaml'
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

        $config = Yaml::parse(file_get_contents($configFilename));

        $basePath = dirname($configFilename);

        $loader = Loader::create([]);
        $project = Project::buildFromConfig($config, 'file://' . $basePath, $loader);
      
        // $variables = array_merge_recursive($jsonData, $project->getVariables());
        // print_r($project->getVariables()); exit();
        $project->generate();
    }
}
