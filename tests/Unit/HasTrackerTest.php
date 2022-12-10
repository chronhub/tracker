<?php

declare(strict_types=1);

namespace Chronhub\Tracker\Tests\Unit;

use Chronhub\Tracker\Draft;
use Chronhub\Message\Message;
use Chronhub\Tracker\Tests\UnitTestCase;
use Chronhub\Contracts\Tracker\MessageStory;
use Chronhub\Tracker\Tests\Double\SomeCommand;
use Chronhub\Tracker\Tests\Stub\ObjectWithTracker;

final class HasTrackerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_instantiate_with_empty_listeners(): void
    {
        $tracker = new ObjectWithTracker();

        $this->assertEmpty($tracker->listeners());
    }

    /**
     * @test
     */
    public function it_order_by_descendant_priorities_when_dispatching_context(): void
    {
        $story = new Draft('dispatch');

        $command = new SomeCommand(['init' => 4]);
        $story->withMessage(new Message($command));

        $tracker = new ObjectWithTracker();

        $firstSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(2, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 1]);

            $story->withMessage(new Message($command));
        };

        $secondSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(3, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 2]);

            $story->withMessage(new Message($command));
        };

        $thirdSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(4, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 3]);

            $story->withMessage(new Message($command));
        };

        $tracker->spyOn('dispatch', $firstSub, -100);
        $tracker->spyOn('dispatch', $secondSub, 1);
        $tracker->spyOn('dispatch', $thirdSub, 100);

        $this->assertCount(3, $tracker->listeners());

        $tracker->disclose($story);

        $this->assertEquals(['init' => 1], $story->message()->event()->content);
    }

    /**
     * @test
     */
    public function it_spy_on_event_and_dispatch_context(): void
    {
        $story = new Draft('dispatch');

        $tracker = new ObjectWithTracker();

        $sub = function (MessageStory $story): void {
            $this->assertEquals('dispatch', $story->currentEvent());
        };

        $tracker->spyOn('dispatch', $sub, 1);

        $this->assertCount(1, $tracker->listeners());

        $tracker->disclose($story);

        $this->assertCount(1, $tracker->listeners());
    }

    /**
     * @test
     */
    public function it_dispatch_context_till_a_truthy_result_callback(): void
    {
        $story = new Draft('dispatch');

        $command = new SomeCommand(['init' => 4]);
        $story->withMessage(new Message($command));

        $tracker = new ObjectWithTracker();

        $firstSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(2, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 1]);
            $story->withMessage(new Message($command));
        };

        $secondSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(3, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 2]);
            $story->withMessage(new Message($command));
        };

        $thirdSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(4, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 3]);
            $story->withMessage(new Message($command));
        };

        $tracker->spyOn('dispatch', $firstSub, -100);
        $tracker->spyOn('dispatch', $secondSub, 1);
        $tracker->spyOn('dispatch', $thirdSub, 100);

        $this->assertCount(3, $tracker->listeners());

        $tracker->discloseUntil($story, function (MessageStory $story): bool {
            return $story->message()->event()->content['init'] === 3;
        });

        $this->assertEquals(['init' => 3], $story->message()->event()->content);
    }

    /**
     * @test
     */
    public function it_stop_propagation_of_event(): void
    {
        $story = new Draft('dispatch');

        $command = new SomeCommand(['init' => 4]);
        $story->withMessage(new Message($command));

        $tracker = new ObjectWithTracker();

        $firstSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(2, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 1]);
            $story->withMessage(new Message($command));
        };

        $secondSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(3, $story->message()->event()->content['init']);

            $story->stop(true);
        };

        $thirdSub = function (MessageStory $story): void {
            $this->assertInstanceOf(SomeCommand::class, $story->message()->event());
            $this->assertEquals(4, $story->message()->event()->content['init']);

            $command = new SomeCommand(['init' => 3]);
            $story->withMessage(new Message($command));
        };

        $tracker->spyOn('dispatch', $firstSub, -100);
        $tracker->spyOn('dispatch', $secondSub, 1);
        $tracker->spyOn('dispatch', $thirdSub, 100);

        $this->assertCount(3, $tracker->listeners());

        $tracker->disclose($story);

        $this->assertEquals(['init' => 3], $story->message()->event()->content);
    }
}
