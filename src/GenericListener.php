<?php

declare(strict_types=1);

namespace Chronhub\Tracker;

use Chronhub\Contracts\Tracker\Listener;

final class GenericListener implements Listener
{
    /**
     * @var callable
     */
    private $eventStory;

    public function __construct(public readonly string $eventName,
                                callable $eventStory,
                                public readonly int $eventPriority)
    {
        $this->eventStory = $eventStory;
    }

    public function callback(): callable
    {
        return $this->eventStory;
    }

    public function name(): string
    {
        return $this->eventName;
    }

    public function priority(): int
    {
        return $this->eventPriority;
    }

    public function story(): callable
    {
        return $this->eventStory;
    }
}
