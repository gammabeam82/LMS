<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveZipFileCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this->setName('app:remove-zip-file');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();
		$user = $container->get('security.token_storage')
			->getToken()
			->getUser();

		$file = sprintf("%s/%s.zip", $container->getParameter('library'), $user->getId());

		if(false !== file_exists($file)) {
			unlink($file);
		}
	}

}