<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Unit;

use Chronhub\Tracker\Draft;
use Chronhub\Testing\UnitTestCase;
use Chronhub\Tracker\TrackMessage;

final class TrackMessageTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_start_new_story(): void
    {
        $tracker = new TrackMessage();

        $story = $tracker->newStory('dispatch');

        $this->assertInstanceOf(Draft::class, $story);

        $this->assertEquals('dispatch', $story->currentEvent());
    }
}
