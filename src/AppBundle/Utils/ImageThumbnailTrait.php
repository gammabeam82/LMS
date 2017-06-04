<?php

namespace AppBundle\Utils;

use claviska\SimpleImage;

trait ImageThumbnailTrait
{
	/**
	 * @param string $file
	 * @param string $path
	 * @param int $width
	 * @param int $height
	 *
	 * @return string
	 */
	public function generateThumbnail($file, $path, $width = 300, $height = 400)
	{
		$filename = sprintf("%s/thumb-%s.png", $path, uniqid());

		$thumbnail = new SimpleImage();

		$thumbnail
			->fromFile($file)
			->bestFit($width, $height)
			->toFile($filename, 'image/png');

		return $filename;
	}
}