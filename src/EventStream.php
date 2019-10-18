<?php
namespace Stk2k\EventStream;

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
    public function channel(string $channel_id, EventSourceInterface $source = null, EventEmitterInterface $emitter = null) : EventChannel
    {
        if (isset($this->channel_list[$channel_id])){
            return $this->channel_list[$channel_id];
        }

        $channel = new EventChannel($source, $emitter);
        $this->channel_list[$channel_id] = $channel;

        return $channel;
    }

    /**
     * Update auto flush flags in all channels
     *
     * @param bool $auto_flush
     *
     * @return EventStream
     */
    public function setAutoFlush(bool $auto_flush) : self
    {
        foreach($this->channel_list as $channel)
        {
            $channel->setAutoFlush($auto_flush);
        }
        return $this;
    }

    /**
     * flush event in all channels
     *
     * @return EventStream
     */
    public function flush() : self
    {
        foreach($this->channel_list as $channel)
        {
            $channel->flush();
        }
        return $this;
    }
}