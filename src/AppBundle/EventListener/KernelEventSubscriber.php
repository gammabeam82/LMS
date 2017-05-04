<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\User;


class KernelEventSubscriber implements EventSubscriberInterface
{
	/**
	 * @var KernelInterface
	 */
	private $kernel;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * KernelEventSubscriber constructor.
	 * @param KernelInterface $kernel
	 * @param TokenStorageInterface $tokenStorage
	 * @param $path
	 */
	public function __construct(KernelInterface $kernel, TokenStorageInterface $tokenStorage, $path)
	{
		$this->kernel = $kernel;
		$this->tokenStorage = $tokenStorage;
		$this->path = $path;
	}

	/**
	 * {@inheritdoc}
	 */
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
		$token = $this->tokenStorage->getToken();

		if(null === $token) {
			return;
		}

		$user = $token->getUser();

		if(false === $user instanceof User) {
			return;
		}

		$file = sprintf("%s/%s.zip", $this->path, $user->getId());

		if(file_exists($file)) {
			unlink($file);
		}
	}
}