<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;


class KernelEventSubscriber implements EventSubscriberInterface
{
	/**
	 * @var KernelInterface
	 */
	private $kernel;

	/**
	 * KernelEventSubscriber constructor.
	 * @param KernelInterface $kernel
	 */
	public function __construct(KernelInterface $kernel)
	{
		$this->kernel = $kernel;
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::TERMINATE => [
				['processEvent', 0]
			]
		];
	}

	public function processEvent()
	{
		$application = new Application($this->kernel);
		$application->setAutoExit(false);

		$input = new ArrayInput([
			'command' => 'app:remove-zip-files'
		]);

		$application->run($input, new NullOutput());
	}
}