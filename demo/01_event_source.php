<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\SimpleEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;
use Stk2k\EventStream\Event;

echo PHP_EOL . 'Number event source demo:' . PHP_EOL;

class NumberEventSource implements EventSourceInterface
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = array('one', 'two', 'three');
    }
    public function canPush() : bool {
        return false;
    }
    public function push(Event $e) : EventSourceInterface {
        return $this;
    }
    public function next() {
        $number = array_shift($this->numbers);
        return $number ? new Event('number',$number) : null;
    }
}
  
// create event stream and setup callback, then flush all events
(new EventStream())
    ->channel('my channel', new NumberEventSource(), new SimpleEventEmitter())
    ->listen('number', function(Event $e){
        echo 'received number='.$e->getPayload(), PHP_EOL;
    })
    ->flush();

// Number event source demo:
// received number=one
// received number=two
// received number=three

// you can not push event to unpushable event source
try{
    (new NumberEventSource())->push(new Event('number','four'));   // throws EventSourceIsNotPushableException
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}

echo PHP_EOL . 'Pushable event source demo:' . PHP_EOL;

class PushableNumberEventSource extends NumberEventSource
{
    public function canPush() : bool {
        return true;
    }
    public function push(Event $e) : EventSourceInterface {
        if ($e->getName() === 'number'){
            $this->numbers[] = $e->getPayload();
        }
        return $this;
    }
}
  
// you acn push event to pushable event source
try{
    (new EventStream())
        ->channel('my channel')
        ->source((new PushableNumberEventSource())->push(new Event('number','four')))
        ->emitter(new SimpleEventEmitter())
        ->listen('number', function(Event $e){
                echo 'received number='.$e->getPayload(), PHP_EOL;
            })
        ->flush()
        ->push(new Event('number', 'five'))
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}

// Pushable event source demo:
// received number=one
// received number=two
// received number=three
// received number=four
// received number=five

