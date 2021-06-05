<?php
namespace stk2k\EventStream\Test;

use PHPUnit\Framework\TestCase;
use stk2k\EventStream\Emitter\RegularExpressionEventEmitter;
use stk2k\EventStream\Event;

class RegularExpressionEventEmitterTest extends TestCase
{
    public function testEmit()
    {
        $emitter = new RegularExpressionEventEmitter();
        
        // simple
        $emitter->listen('/foo/', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );
    
        $emitter->unlisten();
    
        // prefix match
        $emitter->listen('/^f.*$/', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fat', 'orange'));
        $this->assertEquals(new Event('fat', 'orange'), $event_received );
    
        $emitter->unlisten();
    
    
        // suffix match
        $emitter->listen('/^.*oo$/', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('zoo', 'orange'));
        $this->assertEquals(new Event('zoo', 'orange'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('foo_', 'kiwi'));
        $this->assertNull($event_received);
    
        $emitter->unlisten();
    
    
        // partial match
        $emitter->listen('/^f.*o$/', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('zoo', 'orange'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo_', 'kiwi'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('falao', 'peach'));
        $this->assertEquals(new Event('falao', 'peach'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fo', 'pear'));
        $this->assertEquals(new Event('fo', 'pear'), $event_received );
    
        $emitter->unlisten();
    }
}