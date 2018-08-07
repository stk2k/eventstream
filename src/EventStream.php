<?php
namespace EventStream;

/**
 * Event dispatcher class
 */
class EventStream
{
    /** @var EventChannel[] */
    private $channel_list;

    /**
     * Create channel
     *
     * @param string $channel_id
     * @param EventSourceInterface $source
     * @param EventEmitterInterface $emitter
     *
     * @return EventChannel
     */
    public function channel($channel_id, $source = null, $emitter = null)
    {
        if (isset($this->channel_list[$channel_id])){
            return $this->channel_list[$channel_id];
        }

        $channel = new EventChannel($source, $emitter);
        $this->channel_list[$channel_id] = $channel;

        return $channel;
    }
}