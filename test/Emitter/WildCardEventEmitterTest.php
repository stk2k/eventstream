<?php

use Stk2k\EventStream\Emitter\WildCardEventEmitter;

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
        $events = [];
        $args = [];
        $emitter->listen('*oo', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('foa', 'orange');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('fo', 'kiwi');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('fao', 'peach');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('_foo', 'pear');
        $this->assertEquals(['foo', '_foo'], $events );
        $this->assertEquals(['apple', 'pear'], $args );
        
        $emitter->emit('oo', 'melon');
        $this->assertEquals(['foo', '_foo', 'oo'], $events );
        $this->assertEquals(['apple', 'pear', 'melon'], $args );
    
        $emitter->unlisten();
    
        // suffix match
        $events = [];
        $args = [];
        $emitter->listen('fo*', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('foa', 'orange');
        $this->assertEquals(['foo', 'foa'], $events );
        $this->assertEquals(['apple', 'orange'], $args );
    
        $emitter->emit('fo', 'kiwi');
        $this->assertEquals(['foo', 'foa', 'fo'], $events );
        $this->assertEquals(['apple', 'orange', 'kiwi'], $args );
    
        $emitter->emit('fao', 'peach');
        $this->assertEquals(['foo', 'foa', 'fo'], $events );
        $this->assertEquals(['apple', 'orange', 'kiwi'], $args );
    
        $emitter->emit('_foo', 'pear');
        $this->assertEquals(['foo', 'foa', 'fo'], $events );
        $this->assertEquals(['apple', 'orange', 'kiwi'], $args );
        
        $emitter->emit('oo', 'melon');
        $this->assertEquals(['foo', 'foa', 'fo'], $events );
        $this->assertEquals(['apple', 'orange', 'kiwi'], $args );
    
        $emitter->unlisten();
    
        // partial match
        $events = [];
        $args = [];
        $emitter->listen('f*o', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
        
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
        
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('foa', 'orange');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('fo', 'kiwi');
        $this->assertEquals(['foo', 'fo'], $events );
        $this->assertEquals(['apple', 'kiwi'], $args );
    
        $emitter->emit('fao', 'peach');
        $this->assertEquals(['foo', 'fo', 'fao'], $events );
        $this->assertEquals(['apple', 'kiwi', 'peach'], $args );
    
        $emitter->emit('_foo', 'pear');
        $this->assertEquals(['foo', 'fo', 'fao'], $events );
        $this->assertEquals(['apple', 'kiwi', 'peach'], $args );
    
        $emitter->unlisten();
    
        // multiple wildcards
        $events = [];
        $args = [];
        $emitter->listen('*.*', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('taxi.driver', 'banana');
        $this->assertEquals(['taxi.driver'], $events );
        $this->assertEquals(['banana'], $args );
    
        $emitter->emit('taxi-driver', 'apple');
        $this->assertEquals(['taxi.driver'], $events );
        $this->assertEquals(['banana'], $args );
    
        $emitter->emit('water.melon', 'orange');
        $this->assertEquals(['taxi.driver', 'water.melon'], $events );
        $this->assertEquals(['banana', 'orange'], $args );
    
        $emitter->emit('david walks around.', 'kiwi');
        $this->assertEquals(['taxi.driver', 'water.melon', 'david walks around.'], $events );
        $this->assertEquals(['banana', 'orange', 'kiwi'], $args );
    
        $emitter->emit('..', 'peach');
        $this->assertEquals(['taxi.driver', 'water.melon', 'david walks around.', '..'], $events );
        $this->assertEquals(['banana', 'orange', 'kiwi', 'peach'], $args );
    
        $emitter->emit('fruits', 'pear');
        $this->assertEquals(['taxi.driver', 'water.melon', 'david walks around.', '..'], $events );
        $this->assertEquals(['banana', 'orange', 'kiwi', 'peach'], $args );
    }
}