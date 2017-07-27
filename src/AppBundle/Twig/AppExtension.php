<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppExtension extends \Twig_Extension
{
	/**
	 * @var Router
	 */
	private $router;

	/**
	 * AppExtension constructor.
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('random_thumbnail', [$this, 'thumbFilter']),
		];
	}

	/**
	 * @param Book $book
	 * @return bool|string
	 */
	public function thumbFilter(Book $book)
	{
		$thumbnails = [];

		foreach ($book->getBookFiles() as $file) {
			/** @var \AppBundle\Entity\File $file */
			if (null !== $file->getThumbnail()) {
				$thumbnails[] = $file->getId();
			}
		}

		if (0 === count($thumbnails)) {
			return false;
		} else {
			$url = $this->router->generate('books_file_download', [
				'id' => $thumbnails[array_rand($thumbnails)],
				'thumbnail' => true
			], UrlGeneratorInterface::ABSOLUTE_URL);
			return $url;
		}
	}
}