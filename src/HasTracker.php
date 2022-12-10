<?php

declare(strict_types=1);

namespace Chronhub\Tracker;

use Illuminate\Support\Collection;
use Chronhub\Contracts\Tracker\Story;
use Chronhub\Contracts\Tracker\Listener;

trait HasTracker
{
    /**
     * @var Collection<Listener>
     */
    private Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection();
    }

    public function spyOn(string $eventName, callable $story, int $priority = 0): Listener
    {
        $listener = new GenericListener($eventName, $story, $priority);

        $this->listeners->push($listener);

        return $listener;
    }

    public function disclose(Story $story): void
    {
        $this->fireEvent($story, null);
    }

    public function discloseUntil(Story $story, callable $callback): void
    {
        $this->fireEvent($story, $callback);
    }

    public function forget(Listener $listener): void
    {
        $this->listeners = $this->listeners->reject(
            fn (Listener $subscriber): bool => $listener === $subscriber
        );
    }

    public function listeners(): Collection
    {
        return clone $this->listeners;
    }

    /**
     * Dispatching event and consume message
     */
    private function fireEvent(Story $story, ?callable $callback): void
    {
        $currentEvent = $story->currentEvent();

        $this->listeners
            ->filter(fn (Listener $listener): bool => $currentEvent === $listener->eventName)
            ->sortByDesc(fn (Listener $listener): int => $listener->eventPriority, SORT_NUMERIC)
            ->each(function (Listener $listener) use ($story, $callback): bool {
                return $this->handleListener($listener, $story, $callback);
            });
    }

    /**
     * Consume message and stop propagation of event if requested
     */
    private function handleListener(Listener $listener, Story $story, ?callable $callback): bool
    {
        $storyHandler = $listener->story();

        $storyHandler($story);

        if ($story->isStopped()) {
            return false;
        }

        if ($callback && true === $callback($story)) {
            return false;
        }

        return true;
    }
}
