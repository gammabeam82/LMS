<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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

		$path = $this->getContainer()->getParameter('library');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$bookFiles = $em->createQueryBuilder()
			->select('f.name')
			->from('AppBundle:File', 'f')
			->getQuery()
			->execute();

		$bookFiles = array_column($bookFiles, 'name');

		$orphMask = sprintf("%s/*.txt", $path);
		$zipMask = sprintf("%s/*.zip", $path);

		$files = array_merge(glob($orphMask), glob($zipMask));

		$orphanFiles = array_diff($files, $bookFiles);

		$io->section("Files");

		$io->writeln([
			sprintf("Book files: %s", count($bookFiles)),
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
		$io->progressStart(count($files));

		array_map(function ($file) use ($io) {
			unlink($file);
			$io->progressAdvance(1);
		}, $orphanFiles);

		$io->progressFinish();
		$io->success('Done.');
	}

}