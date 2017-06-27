<?php

use EventStream\Emitter\SimpleEventEmitter;

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
        
        $foo_items = array();
        $emitter->listen('foo', function($_) use(&$foo_items){ $foo_items[] = $_; });
        
        $this->assertEquals(0, count($foo_items) );
        
        $emitter->emit('bar', 'banana');
        
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
    
        $this->assertEquals(1, count($foo_items) );
        $this->assertEquals('banana', reset($foo_items) );
        $this->assertEquals('banana', end($foo_items) );
    }
}