<?php
namespace EventStream;

use EventStream\Emitter\SimpleEventEmitter;
use \EventStream\Exception\EventSourceIsNotPushableException;
use EventStream\Source\SimpleEventSource;

class EventChannel
{
    /** @var EventSourceInterface|null */
    private $source;
    
    /** @var EventEmitterInterface|null */
    private $emitter;

    /** @var bool */
    private $auto_flush;

    /**
     * construct
     *
     * @param EventSourceInterface|null $source
     * @param EventEmitterInterface|null $emitter
     */
    public function __construct($source = null, $emitter = null)
    {
        $this->source = $source ? $source : new SimpleEventSource();
        $this->emitter = $emitter ? $emitter : new SimpleEventEmitter();
        $this->auto_flush = false;
    }

    /**
     * change event source
     *
     * @param EventSourceInterface|null $source
     *
     * @return EventChannel
     */
    public function source($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * get event source
     *
     * @return EventSourceInterface|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * change event emitter
     *
     * @param EventEmitterInterface|null $emitter
     *
     * @return EventChannel
     */
    public function emitter($emitter)
    {
        $this->emitter = $emitter;
        return $this;
    }

    /**
     * get event emitter
     *
     * @return EventEmitterInterface|null
     */
    public function getEmitter()
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
    public function push($event, $args = null)
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
                throw new \DomainException('datasource returns invalid event:' . print_r($e,true));
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
    public function listen($event, $listener){
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
    public function setAutoFlush($auto_flush)
    {
        $this->auto_flush = $auto_flush;
        return $this;
    }
}