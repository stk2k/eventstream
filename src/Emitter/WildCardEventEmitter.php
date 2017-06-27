<?php
namespace EventStream\Emitter;

use EventStream\IEventEmitter;

class WildCardEventEmitter extends SimpleEventEmitter implements IEventEmitter
{
    /**
     * get event regular expression
     *
     * @param string $event_key
     *
     * @return string|null
     */
    public function getRegExp($event_key)
    {
        $pos = strpos($event_key,'*');
        if ($pos === false){
            return null;
        }
    
        if ($pos === 0){
            // suffix search
            $reg_exp = '/^.*' . preg_quote(substr($event_key,1),'/') . '$/';
        }
        elseif ($pos === strlen($event_key)-1){
            // prefix search
            $reg_exp = '/^' . preg_quote(substr($event_key,0,$pos),'/') . '.*$/';
        }
        else{
            // partial search
            $reg_exp = '/^' . preg_quote(substr($event_key,0,$pos),'/') . '.*' . preg_quote(substr($event_key,$pos+1),'/') . '$/';
        }
        
        return $reg_exp;
    }
    
    /**
     * emit event
     *
     * @param string $event
     * @param mixed $args
     */
    public function emit($event, $args=null)
    {
        foreach($this->listeners as $event_key => $listers_of_event){
            if ($event_key == $event){
                foreach ($listers_of_event as $listener){
                    call_user_func($listener,$args,$event);
                }
            }
            else {
                $reg_exp = $this->getRegExp($event_key);
                if ($reg_exp && preg_match($reg_exp,$event)){
                    foreach ($listers_of_event as $listener){
                        call_user_func($listener,$args,$event);
                    }
                }
            }
        }
    }
}