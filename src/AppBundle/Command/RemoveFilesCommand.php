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
		$this
			->setName('app:remove-files')
			->setDescription('...');
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

		$pattern = sprintf("%s/*.txt", $this->getContainer()->getParameter('library'));

		$orphanFiles = array_diff(glob($pattern), $bookFiles);

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
		$io->progressStart(count($orphanFiles));

		foreach ($orphanFiles as $file) {
			unlink($file);
			$io->progressAdvance(1);
		}

		$io->progressFinish();
		$io->success('Done.');
	}

}