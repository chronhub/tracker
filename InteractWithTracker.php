<?php

declare(strict_types=1);

namespace Storm\Tracker;

use Illuminate\Support\Collection;
use Storm\Contract\Tracker\EventListener;
use Storm\Contract\Tracker\Story;
use Storm\Reporter\Loader\LoadSubscriberClass;

trait InteractWithTracker
{
    private Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection();
    }

    public function watch(object $subscriber): array
    {
        if (! $subscriber instanceof EventListener) {
            $subscriber = LoadSubscriberClass::from($subscriber);
        }

        return Collection::wrap($subscriber)->each(function (EventListener $listener): void {
            $this->listeners->push($listener);
        })->toArray();
    }

    public function disclose(Story $story): void
    {
        $this->fireEvent($story, null);
    }

    public function discloseUntil(Story $story, callable $callback): void
    {
        $this->fireEvent($story, $callback);
    }

    public function forget(EventListener $listener): void
    {
        $this->listeners = $this->listeners->reject(
            static fn (EventListener $subscriber): bool => $listener === $subscriber
        );
    }

    public function listeners(): Collection
    {
        return clone $this->listeners;
    }

    /**
     * Dispatch event and handle message
     */
    private function fireEvent(Story $story, ?callable $callback): void
    {
        $this->listeners
            ->filter(static fn (EventListener $listener): bool => $story->currentEvent() === $listener->name())
            ->sortByDesc(static fn (EventListener $listener): int => $listener->priority(), SORT_NUMERIC)
            ->each(static function (EventListener $listener) use ($story, $callback): bool {
                $listener->story()($story);

                if ($story->isStopped()) {
                    return false;
                }

                return ! ($callback && true === $callback($story));
            });
    }
}
