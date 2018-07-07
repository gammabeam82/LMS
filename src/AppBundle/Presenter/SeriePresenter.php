<?php

namespace AppBundle\Presenter;

use AppBundle\Entity\Serie;

class SeriePresenter
{
    /**
     * @var Serie
     */
    private $serie;

    /**
     * SeriePresenter constructor.
     *
     * @param Serie $serie
     */
    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }

    /**
     * @param $method
     * @param null $args
     *
     * @return mixed
     */
    public function __call($method, $args = null)
    {
        return call_user_func_array([$this->serie, $method], $args);
    }

    /**
     * @return int
     */
    public function getBooksCount(): int
    {
        return count($this->serie->getBooks());
    }
}
