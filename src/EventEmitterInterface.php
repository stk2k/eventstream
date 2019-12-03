<?php
declare(strict_types=1);

namespace Stk2k\EventStream;

interface EventEmitterInterface
{
    /**
     * add a listener callback to event emitter
     *
     * @param string $event
     * @param callable $listener
     */
    public function listen(string $event, callable $listener);
    
    /**
     * remove a listener callback from event emitter
     *
     * @param string|null $event
     * @param callable|null $listener
     */
    public function unlisten(string $event = null, callable $listener = null);
    
    /**
     * emit event
     *
     * @param Event $event
     */
    public function emit(Event $event);
}
