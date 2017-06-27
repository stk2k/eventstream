<?php
namespace EventStream;

use \EventStream\Exception\EventSourceIsNotPushableException;

/**
 * Event dispatcher class
 */
class EventStream
{
    private $source;
    private $emitter;
    
    /**
     * construct
     *
     * @param IEventSource|null $source
     * @param IEventEmitter|null $emitter
     */
    public function __construct($source=null, $emitter=null){
        $this->source = $source;
        $this->emitter = $emitter;
    }
    
    /**
     * change event source
     *
     * @param IEventSource|null $source
     *
     * @return EventStream
     */
    public function source($source)
    {
        $this->source = $source;
        return $this;
    }
    
    /**
     * get event source
     *
     * @return IEventSource|null
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * change event emitter
     *
     * @param IEventEmitter|null $emitter
     *
     * @return EventStream
     */
    public function emitter($emitter)
    {
        $this->emitter = $emitter;
        return $this;
    }
    
    /**
     * get event emitter
     *
     * @return IEventEmitter|null
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
     * @return EventStream
     *
     * @throws EventSourceIsNotPushableException, OverflowException
     */
    public function push($event, $args = null, $flush = false)
    {
        if ($this->source){
            if (!$this->source->canPush($event)){
                throw new EventSourceIsNotPushableException('event source is full');
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
     * @return EventStream
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
     * @return EventStream
     */
    public function listen($event, $listener){
        if (!$this->emitter){
            return $this;
        }
        $this->emitter->listen($event, $listener);
        return $this;
    }
}