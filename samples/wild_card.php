<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use EventStream\EventStream;
use EventStream\EventSourceInterface;
use EventStream\Emitter\WildCardEventEmitter;
use \EventStream\Exception\EventSourceIsNotPushableException;

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
try{
    (new EventStream())
        ->channel('my channel', new WildCardEventSource(), new WildCardEventEmitter())
        ->listen('user.*', function($_, $event){
            echo 'received ' . $event . '='.$_, PHP_EOL;
        })
        ->listen('*.address', function($_, $event){
            echo 'received ' . $event . '='.$_, PHP_EOL;
        })
        ->flush()
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

