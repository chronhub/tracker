<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Unit;

use Chronhub\Tracker\TrackMessage;
use Chronhub\Tracker\Tests\UnitTestCase;
use Chronhub\Tracker\Tests\Stub\ObjectWithTracking;

final class ProvideTrackingTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_add_listeners_to_be_untracked(): void
    {
        $tracker = new TrackMessage();

        $tracking = new ObjectWithTracking();

        $this->assertEmpty($tracking->getTrackedListeners());

        $tracking->addOnDispatch($tracker, function (): void {
            //
        }, 1);

        $this->assertCount(1, $tracking->getTrackedListeners());
    }

    /**
     * @test
     */
    public function it_untrack_all_listeners_from_subscriber(): void
    {
        $tracker = new TrackMessage();

        $tracking = new ObjectWithTracking();

        $this->assertEmpty($tracking->getTrackedListeners());

        $tracking->addOnDispatch($tracker, function (): void {
            //
        }, 1);

        $tracking->addOnFinalize($tracker, function (): void {
            //
        }, 1);

        $this->assertCount(2, $tracking->getTrackedListeners());

        $tracking->detachFrom($tracker);

        $this->assertEmpty($tracking->getTrackedListeners());
    }
}
