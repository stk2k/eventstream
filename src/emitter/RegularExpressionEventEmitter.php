<?php
declare(strict_types=1);

namespace stk2k\eventstream\emitter;

use stk2k\eventstream\Event;
use stk2k\eventstream\EventEmitterInterface;

class RegularExpressionEventEmitter extends SimpleEventEmitter implements EventEmitterInterface
{
    /**
     * {@inheritDoc}
     */
    public function emit(Event $event)
    {
        if (is_array($this->listeners)){
            foreach($this->listeners as $reg_exp => $listers_of_event){
                if (preg_match($reg_exp,$event->getName())){
                    foreach ($listers_of_event as $listener){
                        call_user_func($listener, $event);
                    }
                }
            }
        }
    }
}