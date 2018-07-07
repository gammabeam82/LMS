<?php

namespace AppBundle\Presenter;

use AppBundle\Entity\Book;

class BookPresenter
{
    /**
     * @var Book
     */
    private $book;

    /**
     * BookPresenter constructor.
     *
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * @param $method
     * @param null $args
     *
     * @return mixed
     */
    public function __call($method, $args = null)
    {
        return call_user_func_array([$this->book, $method], $args);
    }

    /**
     * @return float
     */
    public function getAverageRating(): float
    {
        $ratings = $this->book->getRatings();

        if (0 === count($ratings)) {
            return 0;
        }

        $sum = array_reduce($ratings->toArray(), function ($carry, $rating) {
            $carry += $rating->getValue();
            return $carry;
        }, 0);

        return round(($sum / count($ratings)), 2);
    }
}
