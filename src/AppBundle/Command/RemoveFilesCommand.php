<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveFilesCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this->setName('app:remove-files');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$io = new SymfonyStyle($input, $output);

		$io->text('Fetching data...');

		/** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager();

		$bookFiles = $em->createQueryBuilder()
			->select('b.file')
			->from('AppBundle:Book', 'b')
			->getQuery()
			->execute();

		$bookFiles = array_column($bookFiles, 'file');

		$orphMask = sprintf("%s/*.txt", $this->getContainer()->getParameter('library'));
		$zipMask = sprintf("%s/*.zip", $this->getContainer()->getParameter('library'));

		$orphanFiles = array_diff(glob($orphMask), $bookFiles);
		$files = array_merge($orphanFiles, glob($zipMask));

		$io->section("Files");

		$io->writeln([
			sprintf("Book files: %s", count($bookFiles)),
			sprintf("Orphan & zip files: %s", count($files)),
			''
		]);

		if (0 == count($files)) {
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
		}, $files);

		$io->progressFinish();
		$io->success('Done.');
	}

}