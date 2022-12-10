<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Unit;

use Chronhub\Tracker\GenericListener;
use Chronhub\Contracts\Tracker\Listener;
use Chronhub\Tracker\Tests\UnitTestCase;

final class GenericListenerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_assert_listener(): void
    {
        $story = function (): bool {
            return false;
        };

        $listener = new GenericListener('finalize', $story, -100);

        $this->assertInstanceOf(Listener::class, $listener);

        $this->assertEquals('finalize', $listener->eventName);
        $this->assertEquals('finalize', $listener->name());
        $this->assertEquals(-100, $listener->eventPriority);
        $this->assertEquals(-100, $listener->priority());
        $this->assertSame($story, $listener->callback());
    }
}
