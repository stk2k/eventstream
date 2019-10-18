<?php
namespace Stk2k\EventStream;

use \RuntimeException;

use Stk2k\EventStream\Emitter\SimpleEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;
use Stk2k\EventStream\Source\SimpleEventSource;

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
     * @param string $event
     * @param mixed $args
     *
     * @return EventChannel
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     */
    public function push(string $event, $args = null) : self
    {
        if ($this->source){
            if (!$this->source->canPush($event)){
                throw new EventSourceIsNotPushableException('Event source is full', $event, $args);
            }
            $this->source->push($event, $args);
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
            if (is_string($e)){
                $this->emitter->emit($e);
            }
            elseif (is_array($e) && count($e)===2){
                list($event, $args) = $e;
                $this->emitter->emit($event, $args);
            }
            elseif ($e!==null){
                throw new RuntimeException('datasource returns invalid event:' . print_r($e,true));
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