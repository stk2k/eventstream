<?php
namespace Stk2k\EventStream\Emitter;

use Stk2k\EventStream\EventEmitterInterface;

class RegularExpressionEventEmitter extends SimpleEventEmitter implements EventEmitterInterface
{
    /**
     * emit event
     *
     * @param string $event
     * @param mixed $args
     */
    public function emit(string $event, $args = null)
    {
        if (is_array($this->listeners)){
            foreach($this->listeners as $reg_exp => $listers_of_event){
                if (preg_match($reg_exp,$event)){
                    foreach ($listers_of_event as $listener){
                        call_user_func($listener,$event,$args);
                    }
                }
            }
        }
    }
}