<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\WildCardEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;
use Stk2k\EventStream\Event;

class WildCardEventSource implements EventSourceInterface
{
    protected $events;
    
    public function __construct() {
        $this->events = [
            new Event('hotel.name', 'Tiger Hotel'),
            new Event('hotel.address', 'Tokyo'),
            new Event('hotel.phone_number', '0123456789'),
            new Event('user.name', 'satou tarou'),
            new Event('user.phone_number', '987654321'),
            new Event('user.address', 'Fukuoka'),
        ];
    }
    public function canPush() : bool {
        return true;
    }
    public function push(Event $e) : EventSourceInterface {
        $this->events[] = $e;
        return $this;
    }
    public function next() {
        return array_shift($this->events);
    }
}
  
// listen only user events
try{
    (new EventStream())
        ->channel('my channel', new WildCardEventSource(), new WildCardEventEmitter())
        ->listen('user.*', function(Event $e){
            echo 'received ' . $e->getName() . '='.$e->getPayload(), PHP_EOL;
        })
        ->listen('*.address', function(Event $e){
            echo 'received ' . $e->getName() . '='.$e->getPayload(), PHP_EOL;
        })
        ->push(new Event('user.age', 21))
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}

// received hotel.address=Tokyo
// received user.name=satou tarou
// received user.phone_number=987654321
// received user.address=Fukuoka
// received user.address=Fukuoka
// received user.age=21

