<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\RegularExpressionEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;
use Stk2k\EventStream\Event;

class RegularExpressionEventSource implements EventSourceInterface
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
            new Event('company.name', 'ABC company'),
            new Event('company.phone_number', '000011112222'),
            new Event('company.address', 'Osaka'),
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
  
// listen only hotel and user events
try{
    (new EventStream())
        ->channel('my channel', new RegularExpressionEventSource(), new RegularExpressionEventEmitter())
        ->listen('/[hotel|user]\..*/', function(Event $e){
            echo 'received ' . $e->getName() . '='.$e->getPayload(), PHP_EOL;
        })
        ->flush()
        ->push(new Event('user.age', 21))
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}

// received hotel.name=Tiger Hotel
// received hotel.address=Tokyo
// received hotel.phone_number=0123456789
// received user.name=satou tarou
// received user.phone_number=987654321
// received user.address=Fukuoka
// received user.age=21

