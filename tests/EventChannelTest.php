<?php

use EventStream\EventChannel;
use EventStream\EventSourceInterface;
use EventStream\Emitter\SimpleEventEmitter;

class EventChannelTestEventSource implements EventSourceInterface
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = array('one', 'two', 'three');
    }
    public function canPush($event) {
        return true;
    }
    public function push($event, $args=null) {
        if ($event==='number'){
            $this->numbers[] = $args;
        }
        return $this;
    }
    public function next() {
        $number = array_shift($this->numbers);
        return $number ? array('number',$number) : null;
    }
    public function listEents() {
        return array_values($this->numbers);
    }
}

class EventChannelTest extends PHPUnit_Framework_TestCase
{
    public function testSource()
    {
        $es = new EventChannel();
        $es->source($source=new EventChannelTestEventSource);
        
        $this->assertEquals($source, $es->getSource() );
    }
    public function testGetSource()
    {
        $es = new EventChannel();
        
        $this->assertInstanceOf("EventStream\Source\SimpleEventSource",  $es->getSource());
    }
    public function testEmitter()
    {
        $es = new EventChannel();
        $es->emitter($emitter=new SimpleEventEmitter);
        
        $this->assertEquals($emitter, $es->getEmitter());
    }
    public function testGetEmitter()
    {
        $es = new EventChannel();

        $this->assertInstanceOf("EventStream\Emitter\SimpleEventEmitter",  $es->getEmitter());
    }

    /**
     * @throws \EventStream\Exception\EventSourceIsNotPushableException
     */
    public function testPush()
    {
        $es = new EventChannel();
        $es->source($source=new EventChannelTestEventSource);
        
        $es->push('number', 'four');
        
        $events = $source->listEents();
        
        $this->assertEquals( 4, count($events) );
        $this->assertEquals( 'one', reset($events) );
        $this->assertEquals( 'four', end($events) );
    }
    public function testFlush()
    {
        $es = new EventChannel();
        $es->emitter(new SimpleEventEmitter);
        $es->source($source=new EventChannelTestEventSource);
    
        $events = $source->listEents();
    
        $this->assertEquals( 3, count($events) );
        $this->assertEquals( 'one', reset($events) );
        $this->assertEquals( 'three', end($events) );
        
        $es->flush();
        
        $events = $source->listEents();
        
        $this->assertEquals( 0, count($events) );
        $this->assertFalse( reset($events) );
        $this->assertFalse( end($events) );
    }
    public function testListen()
    {
        $numbers = array();
        $fruits = array();
        
        $es = new EventChannel();
        $es->emitter($emitter=new SimpleEventEmitter);
        $es->source($source=new EventChannelTestEventSource);
        $es->listen('number',function($_) use(&$numbers) {
            $numbers[] = $_;
        });
        $es->listen('fruits',function($_) use(&$fruits) {
            $fruits[] = $_;
        });
        $es->flush();
    
        $this->assertEquals( 2, count($emitter->getListeners()) );
        $this->assertEquals( 3, count($numbers) );
        $this->assertEquals( 'one', reset($numbers) );
        $this->assertEquals( 'three', end($numbers) );
        $this->assertEquals( 0, count($fruits) );
    }

}