<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class RemoveZipFileCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this->setName('app:remove-zip-file');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();
		$token = $container->get('security.token_storage')->getToken();

		if(false === $token instanceof TokenStorageInterface) {
			return;
		}

		$file = sprintf("%s/%s.zip", $container->getParameter('library'), $token->getUser()->getId());

		if(false !== file_exists($file)) {
			unlink($file);
		}
	}

}