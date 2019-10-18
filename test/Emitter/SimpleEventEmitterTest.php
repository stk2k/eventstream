<?php

use Stk2k\EventStream\Emitter\SimpleEventEmitter;

function create_test_listener()
{
    return function($args){
        var_dump($args);
    };
}

class SimpleEventEmitterTest extends PHPUnit_Framework_TestCase
{
    public function testGetListeners()
    {
        $emitter = new SimpleEventEmitter();
        
        $this->assertEquals(0, count($emitter->getListeners()) );
    
        $emitter->listen('test', function(){});
    
        $this->assertEquals(1, count($emitter->getListeners()) );
    }
    public function testListen()
    {
        $emitter = new SimpleEventEmitter();
    
        $this->assertEquals(0, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        
        $emitter->listen('foo', function(){});
    
        $this->assertEquals(1, count($emitter->getListeners()) );
        $this->assertEquals(1, count($emitter->getListeners('foo')) );
        $this->assertEquals(0, count($emitter->getListeners('bar')) );
    
        $emitter->listen('bar', function(){});
    
        $this->assertEquals(2, count($emitter->getListeners()) );
        $this->assertEquals(1, count($emitter->getListeners('foo')) );
        $this->assertEquals(1, count($emitter->getListeners('bar')) );
    }
    public function testUnlisten()
    {
        $emitter = new SimpleEventEmitter();
        
        $this->assertEquals(0, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        
        $a = create_test_listener();
        $b = create_test_listener();
        
        $emitter->listen('foo', $a);
        $emitter->listen('bar', $b);
    
        $this->assertEquals(2, count($emitter->getListeners()) );
        $this->assertEquals(1, count($emitter->getListeners('foo')) );
        $this->assertEquals(1, count($emitter->getListeners('bar')) );
        
        $emitter->unlisten('foo', $a);
        
        $this->assertEquals(1, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        $this->assertEquals(1, count($emitter->getListeners('bar')) );
    
        $emitter->unlisten('bar', $a);
    
        $this->assertEquals(1, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        $this->assertEquals(1, count($emitter->getListeners('bar')) );
    
        $emitter->unlisten('bar');
    
        $this->assertEquals(0, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        $this->assertEquals(0, count($emitter->getListeners('bar')) );
    
        $emitter->listen('foo', $a);
        $emitter->listen('bar', $b);
    
        $emitter->unlisten();
    
        $this->assertEquals(0, count($emitter->getListeners()) );
        $this->assertEquals(0, count($emitter->getListeners('foo')) );
        $this->assertEquals(0, count($emitter->getListeners('bar')) );
    }
    public function testEmit()
    {
        $emitter = new SimpleEventEmitter();

        $events = [];
        $args = [];
        $emitter->listen('foo', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
        
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['banana'], $args );
    }
}