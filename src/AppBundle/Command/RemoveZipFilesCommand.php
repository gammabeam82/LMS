<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveZipFilesCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this->setName('app:remove-zip-files');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$mask = sprintf("%s/*.zip", $this->getContainer()->getParameter('library'));
		array_map(function($file) { unlink($file); }, glob($mask));
	}

}