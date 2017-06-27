<?php

use EventStream\Emitter\RegularExpressionEventEmitter;

class RegularExpressionEventEmitterTest extends PHPUnit_Framework_TestCase
{
    public function testEmit()
    {
        $emitter = new RegularExpressionEventEmitter();
        
        // simple
        $foo_items = array();
        $emitter->listen('/foo/', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->unlisten();
    
        // prefix match
        $foo_items = array();
        $emitter->listen('/^f.*$/', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('fat', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->unlisten();
    
    
        // suffix match
        $foo_items = array();
        $emitter->listen('/^.*oo$/', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('zoo', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->emit('foo_', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->unlisten();
    
    
        // partial match
        $foo_items = array();
        $emitter->listen('/^f.*o$/', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('zoo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('foo_', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('falao', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->emit('fo', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->unlisten();
    }
}