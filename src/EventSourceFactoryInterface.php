<?php
namespace EventStream;

interface EventSourceFactoryInterface
{
    /**
     * Create event source
     *
     * @return EventSourceInterface
     */
    public function createEventSource();
}