<?php
namespace EventStream;

use \EventStream\Exception\EventSourceIsNotPushableException;

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
    public function canPush($event);
    
    /**
     * store event
     *
     * @param string $event
     * @param array|null $args
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     * @return EventSourceInterface
     */
    public function push($event, $args=null);
    
    /**
     * generate next event
     *
     * @return array|string|null       array($event, $args) or $event or null if no events remain in event source.
     */
    public function next();
}
