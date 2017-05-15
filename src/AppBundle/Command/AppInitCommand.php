<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class AppInitCommand extends Command
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
		$commands = [
			['command' => 'doctrine:database:create'],
			['command' => 'doctrine:migrations:migrate'],
			['command' => 'fos:user:create', 'username' => 'testuser', 'email' => 'test@example.com', 'password' => 'p@ssword']
		];

		foreach ($commands as $command) {

			$commandApp = $this->getApplication()->find($command['command']);

			$arguments = new ArrayInput($command);

			$commandApp->run($arguments, $output);
		}
	}


}