<?php
require 'include/autoload.php';

use Stk2k\EventStream\EventStream;
use Stk2k\EventStream\EventSourceInterface;
use Stk2k\EventStream\Emitter\WildCardEventEmitter;
use Stk2k\EventStream\Exception\EventSourceIsNotPushableException;

class WildCardEventSource implements EventSourceInterface
{
    protected $events;
    
    public function __construct() {
        $this->events = array(
            array('hotel.name', 'Tiger Hotel'),
            array('hotel.address', 'Tokyo'),
            array('hotel.phone_number', '0123456789'),
            array('user.name', 'satou tarou'),
            array('user.phone_number', '987654321'),
            array('user.address', 'Fukuoka'),
        );
    }
    public function canPush(string $event) {
        return true;
    }
    public function push(string $event, $args=null) {
        $this->events[] = array($event, $args);
        return $this;
    }
    public function next() {
        return array_shift($this->events);
    }
}
  
// listen only fruits and animal
try{
    (new EventStream())
        ->channel('my channel', new WildCardEventSource(), new WildCardEventEmitter())
        ->listen('user.*', function($event, $args){
            echo 'received ' . $event . '='.$args, PHP_EOL;
        })
        ->listen('*.address', function($event, $args){
            echo 'received ' . $event . '='.$args, PHP_EOL;
        })
        ->push('user.age', 21)
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable: ' . $e->getMessage() . ' event: ' . $e->getEvent();
}

// received hotel.address=Tokyo
// received user.name=satou tarou
// received user.phone_number=987654321
// received user.address=Fukuoka
// received user.address=Fukuoka
// received user.age=21

