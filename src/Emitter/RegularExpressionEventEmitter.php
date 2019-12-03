<?php
declare(strict_types=1);

namespace Stk2k\EventStream\Emitter;

use Stk2k\EventStream\Event;
use Stk2k\EventStream\EventEmitterInterface;

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