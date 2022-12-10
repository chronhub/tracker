<?php

declare(strict_types=1);

namespace Chronhub\Tracker;

use Generator;
use React\Promise\PromiseInterface;
use Chronhub\Contracts\Message\Envelop;
use Chronhub\Contracts\Tracker\MessageStory;

final class Draft implements MessageStory
{
    use InteractWithStory;

    /**
     * The message instance
     *
     * @var Envelop|null
     */
    private ?Envelop $message;

    /**
     * Transient message
     *
     * A message factory should be responsible
     * to produce a valid message instance
     *
     * @var object|array|null
     */
    private object|array|null $transientMessage = null;

    /**
     * Message handler(s)
     *
     * @var iterable<object>
     */
    private iterable $consumers = [];

    /**
     * Is message handled
     *
     * @var bool
     */
    private bool $acknowledged = false;

    /**
     * Promise interface available for the query reporter
     *
     * @var PromiseInterface|null
     */
    private ?PromiseInterface $promise = null;

    public function withTransientMessage(object|array $transientMessage): void
    {
        $this->transientMessage = $transientMessage;
    }

    public function withMessage(Envelop $message): void
    {
        $this->message = $message;
    }

    public function withConsumers(iterable $consumers): void
    {
        $this->consumers = $consumers;
    }

    public function consumers(): Generator
    {
        yield from $this->consumers;
    }

    public function withPromise(PromiseInterface $promise): void
    {
        $this->promise = $promise;
    }

    public function acked(bool $isMessageHandled): void
    {
        $this->acknowledged = $isMessageHandled;
    }

    public function isAcked(): bool
    {
        return $this->acknowledged;
    }

    public function transientMessage(): null|object|array
    {
        return $this->transientMessage;
    }

    public function pullTransientMessage(): object|array
    {
        $transientMessage = $this->transientMessage;

        $this->transientMessage = null;

        return $transientMessage;
    }

    public function message(): Envelop
    {
        return $this->message;
    }

    public function promise(): ?PromiseInterface
    {
        return $this->promise;
    }
}
