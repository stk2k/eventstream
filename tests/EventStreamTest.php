<?php

use EventStream\EventStream;
use EventStream\IEventSource;
use EventStream\Emitter\SimpleEventEmitter;

class EventStreamTestEventSource implements IEventSource
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

class EventStreamTest extends PHPUnit_Framework_TestCase
{
    public function testSource()
    {
        $es = new EventStream();
        $es->source($source=new EventStreamTestEventSource);
        
        $this->assertEquals($source, $es->getSource() );
    }
    public function testGetSource()
    {
        $es = new EventStream();
        
        $this->assertNull($es->getSource());
    }
    public function testEmitter()
    {
        $es = new EventStream();
        $es->emitter($emitter=new SimpleEventEmitter);
        
        $this->assertEquals($emitter, $es->getEmitter());
    }
    public function testGetEmitter()
    {
        $es = new EventStream();
        
        $this->assertNull( $es->getEmitter() );
    }
    public function testPush()
    {
        $es = new EventStream();
        $es->source($source=new EventStreamTestEventSource);
        
        $es->push('number', 'four');
        
        $events = $source->listEents();
        
        $this->assertEquals( 4, count($events) );
        $this->assertEquals( 'one', reset($events) );
        $this->assertEquals( 'four', end($events) );
    }
    public function testFlush()
    {
        $es = new EventStream();
        $es->emitter(new SimpleEventEmitter);
        $es->source($source=new EventStreamTestEventSource);
    
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
        
        $es = new EventStream();
        $es->emitter($emitter=new SimpleEventEmitter);
        $es->source($source=new EventStreamTestEventSource);
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