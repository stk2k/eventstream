<?php
namespace EventStream;

use \EventStream\Exception\EventSourceIsNotPushableException;

class EventChannel
{
    /** @var EventSourceInterface|null */
    private $source;
    
    /** @var EventEmitterInterface|null */
    private $emitter;

    /**
     * construct
     *
     * @param EventSourceInterface|null $source
     * @param EventEmitterInterface|null $emitter
     */
    public function __construct($source=null, $emitter=null){
        $this->source = $source;
        $this->emitter = $emitter;
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
     * @param boolean $flush
     *
     * @return EventChannel
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     */
    public function push($event, $args = null, $flush = false)
    {
        if ($this->source){
            if (!$this->source->canPush($event)){
                throw new EventSourceIsNotPushableException('Event source is full', $event, $args);
            }
            $this->source->push($event, $args);
            if ($flush){
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
}