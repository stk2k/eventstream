<?php
namespace Stk2k\EventStream;

use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;

/**
 * Event source interface
 */
interface EventSourceInterface
{
    /**
     * check if event source can be pushed a event
     *
     * @param string $event
     *
     * @return bool     true if pushable, false if the event store can not be pushed
     */
    public function canPush(string $event);
    
    /**
     * store event
     *
     * @param string $event
     * @param mixed $args
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     * @return EventSourceInterface
     */
    public function push(string $event, $args = null);
    
    /**
     * generate next event
     *
     * @return array|string|null       array($event, $args) or $event or null if no events remain in event source.
     */
    public function next();
}
