<?php
namespace Stk2k\EventStream\Source;

use Stk2k\EventStream\EventSourceInterface;

class SimpleEventSource implements EventSourceInterface
{
    /** @var array */
    private $queue;

    /**
     * check if event source can be pushed a event
     *
     * @param string $event
     *
     * @return bool     true if pushable, false if the event store can not be pushed
     */
    public function canPush(string $event) : bool
    {
        return true;
    }

    /**
     * store event
     *
     * @param string $event
     * @param mixed $args
     *
     * @return EventSourceInterface
     */
    public function push(string $event, $args=null) : EventSourceInterface
    {
        $this->queue[] = [
            $event, $args
        ];
        return $this;
    }

    /**
     * generate next event
     *
     * @return array|string|null       array($event, $args) or $event or null if no events remain in event source.
     */
    public function next()
    {
        if (empty($this->queue)){
            return null;
        }
        return array_shift($this->queue);
    }
}