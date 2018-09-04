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
     * @param EventSourceFactoryInterface|callable $source_factory
     * @param EventEmitterFactoryInterface|callable $emitter_factory
     *
     * @return EventChannel
     */
    public function channel($channel_id, $source_factory = null, $emitter_factory = null)
    {
        if (isset($this->channel_list[$channel_id])){
            return $this->channel_list[$channel_id];
        }

        $source = null;
        if ($source_factory){
            if (is_callable($source_factory)){
                $source = call_user_func($source_factory);
            }
            else if ($source_factory instanceof EventSourceFactoryInterface){
                $source = $source_factory->createEventSource();
            }
        }

        $emitter = null;
        if ($emitter_factory){
            if (is_callable($emitter_factory)){
                $emitter = call_user_func($emitter_factory);
            }
            else if ($emitter_factory instanceof EventSourceFactoryInterface){
                $emitter = $emitter_factory->createEventEmitter();
            }
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
    public function setAutoFlush($auto_flush)
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
    public function flush()
    {
        foreach($this->channel_list as $channel)
        {
            $channel->flush();
        }
        return $this;
    }
}