<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Unit;

use stdClass;
use RuntimeException;
use Chronhub\Tracker\Draft;
use Chronhub\Testing\UnitTestCase;
use React\Promise\PromiseInterface;
use function iterator_to_array;

final class DraftTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $draft = new Draft('some_event');

        $this->assertEquals('some_event', $draft->currentEvent());
        $this->assertNull($draft->transientMessage());
        $this->assertNull($draft->promise());
        $this->assertFalse($draft->isStopped());
        $this->assertEmpty(iterator_to_array($draft->consumers()));
        $this->assertFalse($draft->hasException());
        $this->assertFalse($draft->isAcked());
        $this->assertNull($draft->exception());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_empty_event(): void
    {
        $draft = new Draft(null);

        $draft->withEvent('dispatch');

        $this->assertEquals('dispatch', $draft->currentEvent());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_event(): void
    {
        $draft = new Draft('finalize');

        $this->assertEquals('finalize', $draft->currentEvent());
    }

    /**
     * @test
     */
    public function it_override_event(): void
    {
        $draft = new Draft('dispatch');

        $this->assertEquals('dispatch', $draft->currentEvent());

        $draft->withEvent('finalize');

        $this->assertEquals('finalize', $draft->currentEvent());
    }

    /**
     * @test
     */
    public function it_set_transient_message(): void
    {
        $draft = new Draft('dispatch');

        $draft->withTransientMessage(new stdClass());

        $this->assertInstanceOf(stdClass::class, $draft->transientMessage());

        $message = $draft->pullTransientMessage();

        $this->assertInstanceOf(stdClass::class, $message);

        $this->assertNull($draft->transientMessage());
    }

    /**
     * @test
     */
    public function it_acknowledge_message(): void
    {
        $draft = new Draft('dispatch');

        $this->assertFalse($draft->isAcked());

        $draft->acked(true);

        $this->assertTrue($draft->isAcked());
    }

    /**
     * @test
     */
    public function it_add_consumers(): void
    {
        $draft = new Draft('dispatch');

        $this->assertEmpty(iterator_to_array($draft->consumers()));

        $consumers = [
            fn (): array => [],
            fn (): array => [],
            fn (): array => [],
            fn (): array => [],
        ];

        $draft->withConsumers($consumers);

        $this->assertEquals($consumers, iterator_to_array($draft->consumers()));
    }

    /**
     * @test
     */
    public function it_set_query_promise(): void
    {
        $draft = new Draft('dispatch');

        $this->assertNull($draft->promise());

        $promise = $this->createMock(PromiseInterface::class);

        $draft->withPromise($promise);

        $this->assertEquals($promise, $draft->promise());
    }

    /**
     * @test
     */
    public function it_hold_exception_raised_during_dispatch(): void
    {
        $draft = new Draft('dispatch');

        $this->assertNull($draft->exception());
        $this->assertFalse($draft->hasException());

        $exception = new RuntimeException('foo');

        $draft->withRaisedException($exception);

        $this->assertTrue($draft->hasException());
        $this->assertEquals($exception, $draft->exception());
    }

    /**
     * @test
     */
    public function it_reset_exception(): void
    {
        $draft = new Draft('dispatch');

        $this->assertNull($draft->exception());
        $this->assertFalse($draft->hasException());

        $exception = new RuntimeException('foo');

        $draft->withRaisedException($exception);

        $this->assertTrue($draft->hasException());
        $this->assertEquals($exception, $draft->exception());

        $draft->resetException();

        $this->assertNull($draft->exception());
        $this->assertFalse($draft->hasException());
    }
}
