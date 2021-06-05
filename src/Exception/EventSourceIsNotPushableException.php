<?php
declare(strict_types=1);

namespace stk2k\EventStream\Exception;

use Exception;
use Throwable;

class EventSourceIsNotPushableException extends Exception
{
    /**
     * EventSourceIsNotPushableException constructor.
     *
     * @param int $code
     * @param Throwable|null $prev
     */
    public function __construct(int $code = 0, Throwable $prev = null)
    {
        parent::__construct('Event source is not pushable.', $code, $prev);
    }
}