<?php
declare(strict_types=1);

namespace stk2k\eventstream;

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
     * @param EventSourceInterface|callable $source
     * @param EventEmitterInterface|callable $emitter
     *
     * @return EventChannel
     */
    public function channel(string $channel_id, $source = null, $emitter = null) : EventChannel
    {
        if (isset($this->channel_list[$channel_id])){
            return $this->channel_list[$channel_id];
        }

        // create event source by callback
        if (is_callable($source)){
            $source = ($source)();
        }

        // create event emitter by callback
        if (is_callable($emitter)){
            $emitter = ($emitter)();
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