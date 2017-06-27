<?php
namespace EventStream;

/**
 * Event emitter interface
 */
interface IEventEmitter
{
    /**
     * add a listener callback to event emitter
     *
     * @param string $event
     * @param callable $listener
     */
    public function listen($event, $listener);
    
    /**
     * remove a listener callback from event emitter
     *
     * @param string|null $event
     * @param callable|null $listener
     */
    public function unlisten($event = null, $listener = null);
    
    /**
     * emit event
     *
     * @param string $event
     * @param mixed $args
     */
    public function emit($event, $args=null);
}
