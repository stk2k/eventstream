<?php
namespace EventStream\Exception;

class EventSourceIsNotPushableException extends \Exception
{
    private $event;
    private $args;

    /**
     * EventSourceIsNotPushableException constructor.
     *
     * @param string $message
     * @param string $event
     * @param mixed $args
     * @param \Throwable|null $previous
     */
    public function __construct($message, $event, $args, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->event = $event;
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }
}