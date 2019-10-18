<?php
/** @noinspection PhpUnusedParameterInspection */
require 'include/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\SimpleEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;

class MultiChannelEventSource implements EventSourceInterface
{
    protected $events;
    
    public function __construct() {
        $this->events = [
            ['fruits', 'apple'],
            ['number', 'three'],
            ['animal', 'lion'],
            ['animal', 'cat'],
            ['number', 'two'],
            ['number', 'one'],
            ['fruits', 'banana'],
            ['fruits', 'orange'],
        ];
    }
    public function canPush(string $event) {
        return true;
    }
    public function push(string $event, $args=null) {
        $this->events[] = [$event, $args];
        return $this;
    }
    public function next() {
        return array_shift($this->events);
    }
}
  
// listen only fruits and animal
try{
    (new EventStream())
        ->channel('my channel')
        ->source(new MultiChannelEventSource())
        ->emitter(new SimpleEventEmitter())
        ->listen('fruits', function($event, $args){
                echo 'received fruits='.$args, PHP_EOL;
            })
        ->listen('animal', function($event, $args){
                echo 'received animal='.$args, PHP_EOL;
            })
        ->push('animal', 'panda')
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable: ' . $e->getMessage() . ' event: ' . $e->getEvent();
}

// received fruits=apple
// received animal=lion
// received animal=cat
// received fruits=banana
// received fruits=orange
// received animal=panda

