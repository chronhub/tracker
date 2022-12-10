<?php

declare(strict_types=1);

namespace Chronhub\Tracker;

use Chronhub\Contracts\Tracker\Listener;
use Chronhub\Contracts\Reporter\Reporter;
use Chronhub\Contracts\Tracker\MessageTracker;

trait ProvideTracking
{
    /**
     * @var array<Listener>
     */
    protected array $listeners = [];

    public function detachFromReporter(MessageTracker $tracker): void
    {
        foreach ($this->listeners as $listener) {
            $tracker->forget($listener);
        }

        $this->listeners = [];
    }

    /**
     * Subscribe to reporter on dispatch event
     * and add listener to the untrack array
     */
    protected function onDispatchEvent(MessageTracker $tracker, callable $story, int $priority): void
    {
        $this->listeners[] = $tracker->spyOn(Reporter::DISPATCH_EVENT, $story, $priority);
    }

    /**
     * Subscribe to reporter on finalize event
     * and add listener to the untrack array
     */
    protected function onFinalizeEvent(MessageTracker $tracker, callable $story, int $priority): void
    {
        $this->listeners[] = $tracker->spyOn(Reporter::FINALIZE_EVENT, $story, $priority);
    }
}
