<?php
namespace EventStream\Emitter;

use EventStream\IEventEmitter;

class RegularExpressionEventEmitter extends SimpleEventEmitter implements IEventEmitter
{
    /**
     * emit event
     *
     * @param string $event
     * @param mixed $args
     */
    public function emit($event, $args=null)
    {
        if (is_array($this->listeners)){
            foreach($this->listeners as $reg_exp => $listers_of_event){
                if (preg_match($reg_exp,$event)){
                    foreach ($listers_of_event as $listener){
                        call_user_func($listener,$args,$event);
                    }
                }
            }
        }
    }
}