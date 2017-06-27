<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use EventStream\EventStream;
use EventStream\IEventSource;
use EventStream\Emitter\SimpleEventEmitter;

class MultiChannelEventSource implements IEventSource
{
    protected $events;
    
    public function __construct() {
        $this->events = array(
            array('fruits', 'apple'),
            array('number', 'three'),
            array('animal', 'lion'),
            array('animal', 'cat'),
            array('number', 'two'),
            array('number', 'one'),
            array('fruits', 'banana'),
            array('fruits', 'orange'),
        );
    }
    public function canPush($event) {
        return true;
    }
    public function push($event, $args=null) {
        $this->events[] = array($event, $args);
        return $this;
    }
    public function next() {
        return array_shift($this->events);
    }
}
  
// listen only fruits and animal
(new EventStream())
    ->source(new MultiChannelEventSource())
    ->emitter(new SimpleEventEmitter())
    ->listen('fruits', function($_){
            echo 'received fruits='.$_, PHP_EOL;
        })
    ->listen('animal', function($_){
            echo 'received animal='.$_, PHP_EOL;
        })
    ->flush();
echo PHP_EOL;

// received fruits=apple
// received animal=lion
// received animal=cat
// received fruits=banana
// received fruits=orange

