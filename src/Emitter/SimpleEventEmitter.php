<?php
declare(strict_types=1);

namespace Stk2k\EventStream\Emitter;

use Stk2k\EventStream\Event;
use Stk2k\EventStream\EventEmitterInterface;

class SimpleEventEmitter implements EventEmitterInterface
{
    /** @var callable[][] */
    protected $listeners;
    
    /**
     * get listeners
     *
     * @param string|null $event
     *
     * @return callable[]
     */
    public function getListeners(string $event = null) : array
    {
        if (!$event){
            return $this->listeners ?? [];
        }
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }
    
    /**
     * add a listener callback to event emitter
     *
     * @param string $event
     * @param callable $listener
     */
    public function listen(string $event, callable $listener){
        $listers_of_event = isset($this->listeners[$event]) ? $this->listeners[$event] : array();
        if (!in_array($listener,$listers_of_event,true)){
            $listers_of_event[] = $listener;
            $this->listeners[$event] = $listers_of_event;
        }
    }
    
    /**
     * remove a listener callback from event emitter
     *
     * @param string|null $event
     * @param callable|null $listener
     */
    public function unlisten(string $event = null, callable $listener = null)
    {
        // if event is null, remove all listeners
        if ($event=== null){
            $this->listeners = null;
            return;
        }
        // if listener parameter set null, all event specific listeners are removed
        if (!$listener){
            unset($this->listeners[$event]);
            return;
        }
        // get event specific listeners
        $listers_of_event = isset($this->listeners[$event]) ? $this->listeners[$event] : null;
        // remove all listeners
        if (is_array($listers_of_event)){
            while(($key=array_search($listener,$listers_of_event,true)) !== false){
                unset($listers_of_event[$key]);
                if (!empty($listers_of_event)){
                    // update listener holder, if listeners still exist
                    $this->listeners[$event] = $listers_of_event;
                }
                else{
                    // clean empty listener holder
                    unset($this->listeners[$event]);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function emit(Event $event)
    {
        $listers_of_event = $this->listeners[$event->getName()] ?? null;
        if ($listers_of_event){
            foreach ($listers_of_event as $listener){
                call_user_func($listener, $event);
            }
        }
    }
}