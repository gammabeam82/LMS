<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppInitCommand extends ContainerAwareCommand
{

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this->setName('app:init');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$password = $this->getContainer()->getParameter('password');

		$commands = [
			['command' => 'doctrine:database:create'],
			['command' => 'doctrine:migrations:migrate'],
			['command' => 'doctrine:fixtures:load'],
			['command' => 'fos:user:create', 'username' => 'testuser', 'email' => 'test@example.com', 'password' => $password],
			['command' => 'fos:user:promote', 'username' => 'testuser', 'role' => 'ROLE_ADMIN'],
		];

		foreach ($commands as $command) {

			$commandApp = $this->getApplication()->find($command['command']);

			$arguments = new ArrayInput($command);

			$commandApp->run($arguments, $output);
		}
	}


}