<?php
namespace EventStream;

interface EventEmitterFactoryInterface
{
    /**
     * Create event emitter
     *
     * @return EventEmitterInterface
     */
    public function createEventEmitter();
}