<?php
declare(strict_types=1);

namespace stk2k\eventstream\source;

use stk2k\eventstream\Event;
use stk2k\eventstream\EventSourceInterface;

class SimpleEventSource implements EventSourceInterface
{
    /** @var Event[] */
    private $queue;

    /**
     * {@inheritDoc}
     */
    public function canPush() : bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function push(Event $event) : EventSourceInterface
    {
        $this->queue[] = $event;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        if (empty($this->queue)){
            return false;
        }
        return array_shift($this->queue);
    }
}