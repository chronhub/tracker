<?php

declare(strict_types=1);

namespace Chronhub\Tracker;

use Chronhub\Contracts\Tracker\MessageStory;
use Chronhub\Contracts\Tracker\MessageTracker;

final class TrackMessage implements MessageTracker
{
    use HasTracker;

    public function newStory(string $eventName): MessageStory
    {
        return new Draft($eventName);
    }
}
