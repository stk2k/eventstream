<?php
declare(strict_types=1);

namespace stk2k\eventstream;

final class Event
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $payload;

    /**
     * Event constructor.
     *
     * @param string $name
     * @param mixed $payload
     */
    public function __construct(string $name, $payload = null)
    {
        $this->name = $name;
        $this->payload = $payload;
    }

    /**
     * Returns event name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns event payload data
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}