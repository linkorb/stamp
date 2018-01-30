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
        //var_dump($project);
        $generator = new Generator($project);
        $generator->generate();
        
    }
}
