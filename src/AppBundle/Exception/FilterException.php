<?php

namespace AppBundle\Exception;

use Exception;

class FilterException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'messages.filter_error';

    /**
     * FilterException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
