<?php
require 'include/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\SimpleEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;

echo PHP_EOL . 'Number event source demo:' . PHP_EOL;

class NumberEventSource implements EventSourceInterface
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = array('one', 'two', 'three');
    }
    public function canPush(string $event) {
        return false;
    }
    public function push(string $event, $args=null) {
        return $this;
    }
    public function next() {
        $number = array_shift($this->numbers);
        return $number ? array('number',$number) : null;
    }
}
  
// create event stream and setup callback, then flush all events
(new EventStream())
    ->channel('my channel', new NumberEventSource(), new SimpleEventEmitter())
    ->listen('number', function($event, $n){
        echo 'received number='.$n, PHP_EOL;
    })
    ->flush();

// Number event source demo:
// received number=one
// received number=two
// received number=three

// you can not push event to unpushable event source
try{
    (new NumberEventSource())->push('number','four');   // throws EventSourceIsNotPushableException
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable: ' . $e->getMessage() . ' event: ' . $e->getEvent();
}

echo PHP_EOL . 'Pushable event source demo:' . PHP_EOL;

class PushableNumberEventSource extends NumberEventSource
{
    public function canPush(string $event) {
        return true;
    }
    public function push(string $event, $args=null) {
        if ($event==='number'){
            $this->numbers[] = $args;
        }
        return $this;
    }
}
  
// you acn push event to pushable event source
try{
    (new EventStream())
        ->channel('my channel')
        ->source((new PushableNumberEventSource())->push('number','four'))
        ->emitter(new SimpleEventEmitter())
        ->listen('number', function($event, $n){
                echo 'received number='.$n, PHP_EOL;
            })
        ->flush()
        ->push('number', 'five')
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable: ' . $e->getMessage() . ' event: ' . $e->getEvent();
}

// Pushable event source demo:
// received number=one
// received number=two
// received number=three
// received number=four
// received number=five

