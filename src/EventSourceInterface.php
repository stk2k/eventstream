<?php
declare(strict_types=1);

namespace stk2k\eventstream;

use stk2k\eventstream\Exception\EventSourceIsNotPushableException;

/**
 * Event source interface
 */
interface EventSourceInterface
{
    /**
     * check if event source can be pushed a event
     *
     * @return bool     true if pushable, false if the event store can not be pushed
     */
    public function canPush() : bool;
    
    /**
     * store event
     *
     * @param Event $event
     *
     * @return EventSourceInterface
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     */
    public function push(Event $event) : EventSourceInterface;
    
    /**
     * generate next event
     *
     * @return Event|null|false       Event object or null|false if no events remain in event source.
     */
    public function next();
}
