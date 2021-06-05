<?php
declare(strict_types=1);

namespace stk2k\EventStream;

use \RuntimeException;

use stk2k\EventStream\Emitter\SimpleEventEmitter;
use stk2k\EventStream\Exception\EventSourceIsNotPushableException;
use stk2k\EventStream\Source\SimpleEventSource;

class EventChannel
{
    /** @var EventSourceInterface */
    private $source;
    
    /** @var EventEmitterInterface */
    private $emitter;

    /** @var bool */
    private $auto_flush;

    /**
     * construct
     *
     * @param EventSourceInterface $source
     * @param EventEmitterInterface $emitter
     */
    public function __construct(EventSourceInterface $source = null, EventEmitterInterface $emitter = null)
    {
        $this->source = $source ? $source : new SimpleEventSource();
        $this->emitter = $emitter ? $emitter : new SimpleEventEmitter();
        $this->auto_flush = false;
    }

    /**
     * change event source
     *
     * @param EventSourceInterface $source
     *
     * @return EventChannel
     */
    public function source(EventSourceInterface $source) : self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * get event source
     *
     * @return EventSourceInterface
     */
    public function getSource() : EventSourceInterface
    {
        return $this->source;
    }

    /**
     * change event emitter
     *
     * @param EventEmitterInterface $emitter
     *
     * @return EventChannel
     */
    public function emitter($emitter) : self
    {
        $this->emitter = $emitter;
        return $this;
    }

    /**
     * get event emitter
     *
     * @return EventEmitterInterface
     */
    public function getEmitter() : EventEmitterInterface
    {
        return $this->emitter;
    }

    /**
     * store event
     *
     * @param Event $event
     *
     * @return EventChannel
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     */
    public function push(Event $event) : self
    {
        if ($this->source){
            if (!$this->source->canPush()){
                throw new EventSourceIsNotPushableException();
            }
            $this->source->push($event);
            if ($this->auto_flush){
                $this->flush();
            }
        }
        return $this;
    }

    /**
     * flush stream
     *
     * @return EventChannel
     */
    public function flush(){
        if (!$this->source || !$this->emitter){
            return $this;
        }
        while($e = $this->source->next()){
            if ($e instanceof Event){
                $this->emitter->emit($e);
            }
            else{
                throw new RuntimeException('event source returns invalid event:' . print_r($e,true));
            }
        }
        return $this;
    }

    /**
     * listen event
     *
     * @param string $event
     * @param callable $listener
     *
     * @return EventChannel
     */
    public function listen(string $event, callable $listener) : self
    {
        if (!$this->emitter){
            return $this;
        }
        $this->emitter->listen($event, $listener);
        return $this;
    }

    /**
     * Update auto flush flags in all channels
     *
     * @param bool $auto_flush
     *
     * @return EventChannel
     */
    public function setAutoFlush(bool $auto_flush) : self
    {
        $this->auto_flush = $auto_flush;
        return $this;
    }
}