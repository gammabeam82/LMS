<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\File;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class FileTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * FileTransformer constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param File $file
     * @return array
     */
    public function transform(File $file): array
    {
        return [
            'id' => $file->getId(),
            'type' => $file->getType(),
            'size' => $file->getSizeInKb(),
            'url' => $this->router->generate('api_books_file_download', ['id' => $file->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
    }
}
