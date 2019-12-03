<?php
namespace Stk2k\EventStream\Test;

use PHPUnit\Framework\TestCase;
use Stk2k\EventStream\Emitter\WildCardEventEmitter;
use Stk2k\EventStream\Event;

class WildCardEventEmitterTest extends TestCase
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
        $emitter->listen('*oo', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo','apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('foa', 'orange'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('fo', 'kiwi'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('fao', 'peach'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('_foo', 'pear'));
        $this->assertEquals(new Event('_foo', 'pear'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('oo', 'melon'));
        $this->assertEquals(new Event('oo', 'melon'), $event_received );
    
        $emitter->unlisten();
    
        // suffix match
        $emitter->listen('fo*', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('foa', 'orange'));
        $this->assertEquals(new Event('foa', 'orange'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fo', 'kiwi'));
        $this->assertEquals(new Event('fo', 'kiwi'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fao', 'peach'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('_foo', 'pear'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('oo', 'melon'));
        $this->assertNull($event_received);
    
        $emitter->unlisten();
    
        // partial match
        $emitter->listen('f*o', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('bar', 'banana'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('foo', 'apple'));
        $this->assertEquals(new Event('foo', 'apple'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('foa', 'orange'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('fo', 'kiwi'));
        $this->assertEquals(new Event('fo', 'kiwi'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fao', 'peach'));
        $this->assertEquals(new Event('fao', 'peach'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('_foo', 'pear'));
        $this->assertNull($event_received);
    
        $emitter->unlisten();
    
        // multiple wildcards
        $emitter->listen('*.*', function($event) use(&$event_received){ $event_received = $event; });

        $event_received = null;
        $emitter->emit(new Event('taxi.driver', 'banana'));
        $this->assertEquals(new Event('taxi.driver', 'banana'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('taxi-driver', 'apple'));
        $this->assertNull($event_received);

        $event_received = null;
        $emitter->emit(new Event('water.melon', 'orange'));
        $this->assertEquals(new Event('water.melon', 'orange'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('david walks around.', 'kiwi'));
        $this->assertEquals(new Event('david walks around.', 'kiwi'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('..', 'peach'));
        $this->assertEquals(new Event('..', 'peach'), $event_received );

        $event_received = null;
        $emitter->emit(new Event('fruits', 'pear'));
        $this->assertNull($event_received);
    }
}