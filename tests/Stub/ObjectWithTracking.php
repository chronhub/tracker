<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Stub;

use Chronhub\Tracker\ProvideTracking;
use Chronhub\Contracts\Tracker\MessageTracker;

final class ObjectWithTracking
{
    use ProvideTracking;

    /**
     * @return array<Listener>
     */
    public function getTrackedListeners(): array
    {
        return $this->listeners;
    }

    public function addOnDispatch(MessageTracker $tracker, callable $story, int $priority): void
    {
        $this->onDispatchEvent($tracker, $story, $priority);
    }

    public function addOnFinalize(MessageTracker $tracker, callable $story, int $priority): void
    {
        $this->onFinalizeEvent($tracker, $story, $priority);
    }
}
