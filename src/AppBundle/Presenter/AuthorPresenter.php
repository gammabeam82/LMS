<?php

namespace AppBundle\Presenter;

use AppBundle\Entity\Author;

class AuthorPresenter
{
    /**
     * @var Author
     */
    private $author;

    /**
     * AuthorPresenter constructor.
     *
     * @param Author $author
     */
    public function __construct(Author $author)
    {
        $this->author = $author;
    }

    /**
     * @param $method
     * @param null $args
     *
     * @return mixed
     */
    public function __call($method, $args = null)
    {
        return call_user_func_array([$this->author, $method], $args);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return sprintf("%s %s", $this->author->getFirstName(), $this->author->getLastName());
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return sprintf("%s %s", mb_substr($this->author->getFirstName(), 0, 1), $this->author->getLastName());
    }

    /**
     * @return int
     */
    public function getBooksCount(): int
    {
        return count($this->author->getBooks());
    }
}
