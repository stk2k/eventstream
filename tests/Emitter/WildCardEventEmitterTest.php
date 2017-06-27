<?php

use EventStream\Emitter\WildCardEventEmitter;

class WildCardEventEmitterTest extends PHPUnit_Framework_TestCase
{
    public function testGetRegExp()
    {
        $emitter = new WildCardEventEmitter();
        
        $reg_exp = $emitter->getRegExp('foo');
        
        $this->assertNull( $reg_exp );
    
        // prefix seach
        $reg_exp = $emitter->getRegExp('fo*');
    
        $this->assertEquals( '/^fo.*$/', $reg_exp );
    
        // suffix seach
        $reg_exp = $emitter->getRegExp('*oo');
    
        $this->assertEquals( '/^.*oo$/', $reg_exp );
    
        // partial seach
        $foo_exp = $emitter->getRegExp('f*o');
        $bar_exp = $emitter->getRegExp('b*r');
    
        $this->assertEquals( '/^f.*o$/', $foo_exp );
        $this->assertEquals( '/^b.*r$/', $bar_exp );
        
        // multiple wildcards
        $reg_exp = $emitter->getRegExp('*.*');
    
        $this->assertEquals( '/^.*\..*$/', $reg_exp );
        
    }
    
    public function testEmit()
    {
        $emitter = new WildCardEventEmitter();
        
        
        // prefix match
        $foo_items = array();
        $emitter->listen('*oo', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('foa', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('fo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('fao', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('_foo', 'banana');
        $this->assertEquals(2, count($foo_items) );
        
        $emitter->emit('oo', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->unlisten();
    
        // suffix match
        $foo_items = array();
        $emitter->listen('fo*', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('foa', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->emit('fo', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->emit('fao', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->emit('_foo', 'banana');
        $this->assertEquals(3, count($foo_items) );
        
        $emitter->emit('oo', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->unlisten();
    
        // partial match
        $foo_items = array();
        $emitter->listen('f*o', function($_) use(&$foo_items){ $foo_items[] = $_; });
        
        $this->assertEquals(0, count($foo_items) );
        
        $emitter->emit('bar', 'banana');
        $this->assertEquals(0, count($foo_items) );
        
        $emitter->emit('foo', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('foa', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('fo', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->emit('fao', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->emit('_foo', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->unlisten();
    
        // multiple wildcards
        $foo_items = array();
        $emitter->listen('*.*', function($_) use(&$foo_items){ $foo_items[] = $_; });
    
        $this->assertEquals(0, count($foo_items) );
    
        $emitter->emit('taxi.driver', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('taxi-driver', 'banana');
        $this->assertEquals(1, count($foo_items) );
    
        $emitter->emit('water.melon', 'banana');
        $this->assertEquals(2, count($foo_items) );
    
        $emitter->emit('david walks around.', 'banana');
        $this->assertEquals(3, count($foo_items) );
    
        $emitter->emit('..', 'banana');
        $this->assertEquals(4, count($foo_items) );
    
        $emitter->emit('fruits', 'banana');
        $this->assertEquals(4, count($foo_items) );
    }
}