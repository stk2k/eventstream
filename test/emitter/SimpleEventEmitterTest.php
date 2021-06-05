<?php
declare(strict_types=1);

namespace stk2k\eventstream\test\emitter;

use PHPUnit\Framework\TestCase;
use stk2k\eventstream\emitter\SimpleEventEmitter;
use stk2k\eventstream\Event;

function create_test_listener() : callable
{
    return function($args){
        var_dump($args);
    };
}

class SimpleEventEmitterTest extends TestCase
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

        $emitter->listen('foo', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);
    
        $emitter->emit(new Event('foo', 'banana'));
        $this->assertEquals(new Event('foo', 'banana'), $event_received );
    }
}