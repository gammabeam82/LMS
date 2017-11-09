<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class LogsClearCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:logs-clear');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Delete log files? (y|n) ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $logDir = $this->getContainer()->get('kernel')->getLogDir();

        array_map(function ($file) {
            unlink($file);
        }, glob(sprintf("%s/*.log", $logDir)));

        $io->success('Done.');
    }
}
