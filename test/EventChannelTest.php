<?php /** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace stk2k\eventstream\test;

use PHPUnit\Framework\TestCase;
use stk2k\eventstream\Event;
use stk2k\eventstream\EventChannel;
use stk2k\eventstream\EventSourceInterface;
use stk2k\eventstream\Emitter\SimpleEventEmitter;
use stk2k\eventstream\Source\SimpleEventSource;

class EventChannelTestEventSource implements EventSourceInterface
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = ['one', 'two', 'three'];
    }
    public function canPush() : bool {
        return true;
    }
    public function push(Event $event) : EventSourceInterface {
        if ($event->getName() === 'number'){
            $this->numbers[] = $event->getPayload();
        }
        return $this;
    }
    public function next() {
        if (empty($this->numbers)){
            return false;
        }
        return new Event('number', array_shift($this->numbers));
    }
    public function listStoredNumbers() {
        return $this->numbers;
    }
}

class EventChannelTest extends TestCase
{
    public function testSource()
    {
        $es = new EventChannel();
        $es->source($source=new EventChannelTestEventSource);
        
        $this->assertEquals($source, $es->getSource() );
    }
    public function testGetSource()
    {
        $es = new EventChannel();
        
        $this->assertInstanceOf(SimpleEventSource::class,  $es->getSource());
    }
    public function testEmitter()
    {
        $es = new EventChannel();
        $es->emitter($emitter=new SimpleEventEmitter);
        
        $this->assertEquals($emitter, $es->getEmitter());
    }
    public function testGetEmitter()
    {
        $es = new EventChannel();

        $this->assertInstanceOf(SimpleEventEmitter::class,  $es->getEmitter());
    }

    /**
     * @throws
     */
    public function testPush()
    {
        $es = new EventChannel();
        $es->source($source=new EventChannelTestEventSource);
        
        $es->push(new Event('number', 'four'));
        
        $events = $source->listStoredNumbers();
        
        $this->assertEquals( 4, count($events) );
        $this->assertEquals( 'one', reset($events) );
        $this->assertEquals( 'four', end($events) );
    }
    public function testFlush()
    {
        $es = new EventChannel();
        $es->emitter(new SimpleEventEmitter);
        $es->source($source=new EventChannelTestEventSource);
    
        $events = $source->listStoredNumbers();
    
        $this->assertEquals( 3, count($events) );
        $this->assertEquals( 'one', reset($events) );
        $this->assertEquals( 'three', end($events) );
        
        $es->flush();
        
        $events = $source->listStoredNumbers();
        
        $this->assertEquals( 0, count($events) );
        $this->assertFalse( reset($events) );
        $this->assertFalse( end($events) );
    }
    public function testListen()
    {
        $numbers = [];
        $fruits = [];
        
        $es = new EventChannel();
        $es->emitter($emitter=new SimpleEventEmitter);
        $es->source($source=new EventChannelTestEventSource);
        $es->listen('number',function(Event $event) use(&$numbers) {
            $numbers[] = $event->getPayload();
        });
        $es->listen('fruits',function(Event $event) use(&$fruits) {
            $fruits[] = $event->getPayload();
        });
        $es->flush();
    
        $this->assertEquals( 2, count($emitter->getListeners()) );
        $this->assertEquals( 3, count($numbers) );
        $this->assertEquals( 'one', reset($numbers) );
        $this->assertEquals( 'three', end($numbers) );
        $this->assertEquals( 0, count($fruits) );
    }

}