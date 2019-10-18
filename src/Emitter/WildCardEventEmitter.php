<?php
namespace Stk2k\EventStream\Emitter;

use Stk2k\EventStream\EventEmitterInterface;

class WildCardEventEmitter extends SimpleEventEmitter implements EventEmitterInterface
{
    const PREG_METACHARS = "[]-^+*$\\.?()|!¥a¥b¥c¥d¥h¥n¥q¥w¥z¥n¥t¥0";
    
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
        
        $buffer = '';
        $reg_exp = '';
        $length = strlen($event_key);
        
        for($i=0; $i<$length; $i++){
            $c = $event_key[$i];
            if (strpos(self::PREG_METACHARS,$c) !== false){
                if (!empty($buffer)){
                    $reg_exp .= preg_quote($buffer,'/');
                    $buffer = '';
                }
                if ($c == '*'){
                    $reg_exp .= '.*';
                }
                else{
                    $reg_exp .= preg_quote($c,'/');;
                }
            }
            else{
                $buffer .= $c;
            }
        }
        if (!empty($buffer)){
            $reg_exp .= preg_quote($buffer,'/');
        }
        
        return '/^' . $reg_exp . '$/';
    }
    
    /**
     * emit event
     *
     * @param string $event
     * @param mixed $args
     */
    public function emit(string $event, $args = null)
    {
        if (is_array($this->listeners)){
            foreach($this->listeners as $event_key => $listers_of_event){
                if ($event_key == $event){
                    foreach ($listers_of_event as $listener){
                        call_user_func($listener,$event,$args);
                    }
                }
                else {
                    $reg_exp = $this->getRegExp($event_key);
                    if ($reg_exp && preg_match($reg_exp,$event)){
                        foreach ($listers_of_event as $listener){
                            call_user_func($listener,$event,$args);
                        }
                    }
                }
            }
        }
        }
}