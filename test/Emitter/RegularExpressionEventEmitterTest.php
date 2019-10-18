<?php

use Stk2k\EventStream\Emitter\RegularExpressionEventEmitter;

class RegularExpressionEventEmitterTest extends PHPUnit_Framework_TestCase
{
    public function testEmit()
    {
        $emitter = new RegularExpressionEventEmitter();
        
        // simple
        $events = [];
        $args = [];
        $emitter->listen('/foo/', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });
    
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->unlisten();
    
        // prefix match
        $events = [];
        $args = [];
        $emitter->listen('/^f.*$/', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('fat', 'orange');
        $this->assertEquals(['foo', 'fat'], $events );
        $this->assertEquals(['apple', 'orange'], $args );
    
        $emitter->unlisten();
    
    
        // suffix match
        $events = [];
        $args = [];
        $emitter->listen('/^.*oo$/', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('zoo', 'orange');
        $this->assertEquals(['foo', 'zoo'], $events );
        $this->assertEquals(['apple', 'orange'], $args );
    
        $emitter->emit('foo_', 'kiwi');
        $this->assertEquals(['foo', 'zoo'], $events );
        $this->assertEquals(['apple', 'orange'], $args );
    
        $emitter->unlisten();
    
    
        // partial match
        $events = [];
        $args = [];
        $emitter->listen('/^f.*o$/', function($event, $arg) use(&$events, &$args){ $events[] = $event; $args[] = $arg; });

        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('bar', 'banana');
        $this->assertEquals([], $events );
        $this->assertEquals([], $args );
    
        $emitter->emit('foo', 'apple');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('zoo', 'orange');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('foo_', 'kiwi');
        $this->assertEquals(['foo'], $events );
        $this->assertEquals(['apple'], $args );
    
        $emitter->emit('falao', 'peach');
        $this->assertEquals(['foo', 'falao'], $events );
        $this->assertEquals(['apple', 'peach'], $args );
    
        $emitter->emit('fo', 'pear');
        $this->assertEquals(['foo', 'falao', 'fo'], $events );
        $this->assertEquals(['apple', 'peach', 'pear'], $args );
    
        $emitter->unlisten();
    }
}