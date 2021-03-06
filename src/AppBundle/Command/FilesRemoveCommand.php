<?php

namespace AppBundle\Command;

use AppBundle\Entity\ExportItem;
use AppBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class FilesRemoveCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:files-remove');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Fetching data...');

        $libraryPath = $this->getContainer()->getParameter('library');
        $repo = $this->getContainer()->get('doctrine')->getRepository(File::class);
        $bookFiles = $repo->findAllFileNames();

        $exportPath = $this->getContainer()->getParameter('export');
        $repo = $this->getContainer()->get('doctrine')->getRepository(ExportItem::class);
        $exportFiles = array_column($repo->findAllFileNames(), 'filename');

        $bookFiles = array_merge(
            array_column($bookFiles, 'name'),
            array_filter(array_column($bookFiles, 'thumbnail'), function ($item) {
                return !empty($item);
            })
        );

        $allBookFiles = glob(sprintf("%s/*.*", $libraryPath));
        $allExportFiles = glob(sprintf("%s/*.*", $exportPath));

        $orphanBookFiles = array_diff($allBookFiles, $bookFiles);
        $orphanExportFiles = array_diff($allExportFiles, $exportFiles);

        $orphanFiles = array_merge($orphanBookFiles, $orphanExportFiles);

        $io->section("Files");

        $io->writeln([
            sprintf("Book files: %s", count($bookFiles)),
            sprintf("Export files: %s", count($exportFiles)),
            sprintf("Orphan files: %s", count($orphanFiles)),
            ''
        ]);

        if (0 == count($orphanFiles)) {
            return;
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Delete files? (y|n) ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $io->writeln(['', 'Executing...', '']);
        $io->progressStart(count($orphanFiles));

        array_map(function ($file) use ($io) {
            unlink($file);
            $io->progressAdvance(1);
        }, $orphanFiles);

        $io->progressFinish();
        $io->success('Done.');
    }
}
