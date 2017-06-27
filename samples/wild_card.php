<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use EventStream\EventStream;
use EventStream\IEventSource;
use EventStream\Emitter\WildCardEventEmitter;

class WildCardEventSource implements IEventSource
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
(new EventStream())
    ->source(new WildCardEventSource())
    ->emitter(new WildCardEventEmitter())
    ->listen('user.*', function($_, $event){
            echo 'received ' . $event . '='.$_, PHP_EOL;
        })
    ->listen('*.address', function($_, $event){
            echo 'received ' . $event . '='.$_, PHP_EOL;
        })
    ->flush()
    ->push('user.age', 21)
    ->flush();
echo PHP_EOL;

// received hotel.address=Tokyo
// received user.name=satou tarou
// received user.phone_number=987654321
// received user.address=Fukuoka
// received user.address=Fukuoka
// received user.age=21

