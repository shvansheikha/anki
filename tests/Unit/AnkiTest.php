<?php

namespace Jackwestin\AnkiSandbox\Tests\Unit;

use Jackwestin\AnkiSandbox\app\Service\SM2;
use Jackwestin\AnkiSandbox\app\Utilities\Settings;
use PHPUnit\Framework\TestCase;

class AnkiTest extends TestCase
{
    private $sm2;

    private $interval = [
        '1m' => 60,
        '6m' => 360,
        '10m' => 600,
        '1d' => 86400,
        '4d' => 345600,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->sm2 = new SM2();
    }

    private function makeCard($new = true, $interval = 0, $step = 0, $ease = 0)
    {
        return [
            "id" => 1,
            "title" => "card 1",
            "answer" => "",
            "new" => $new,
            "ease" => $ease,
            "interval" => $interval,
            "step" => $step
        ];
    }

    /** @test */
    public function it_can_change_new_column()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "again");

        $this->assertFalse($card['new']);
    }

    /** @test */
    public function it_can_update_step_for_new_card_if_answer_was_good()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "good");

        $this->assertEquals($card['step'], 1);
    }

    /** @test */
    public function it_can_update_step_for_new_card_if_answer_was_easy()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "easy");

        $this->assertEquals($card['step'], 2);
    }

    /** @test */
    public function it_can_update_step_after_three_time()
    {
        $card = $this->makeCard(false, $this->interval['1d'], 2, 250);

        $card = $this->sm2->cardAnswer($card, "easy");

        $this->assertEquals($card['step'], 3);
    }

    /** @test */
    public function it_can_calculate_interval_for_again_after_2_step()
    {
        $ease = 250;
        $card = $this->makeCard(false, $this->interval['1d'], 2, $ease);

        $card = $this->sm2->cardAnswer($card, 'again');

        $ivl = $this->interval['1d'] / 2;
        $this->assertEquals($ivl, $card['interval']);
        $this->assertEquals($ease - Settings::$AGAIN_EASE, $card['ease']);
    }

    /** @test */
    public function it_can_calculate_interval_for_hard_after_2_step()
    {
        $ease = 250;
        $card = $this->makeCard(false, $this->interval['1d'], 2, $ease);

        $card = $this->sm2->cardAnswer($card, 'hard');

        $ivl = $this->interval['1d'] / 2;
        $this->assertEquals($ivl, $card['interval']);
        $this->assertEquals(1, $card['step']);
        $this->assertEquals($ease - Settings::$HARD_EASE, $card['ease']);
    }

    /** @test */
    public function it_can_calculate_interval_for_good_after_2_step()
    {
        $ease = 250;
        $card = $this->makeCard(false, $this->interval['1d'], 2, $ease);

        $card = $this->sm2->cardAnswer($card, 'good');

        $ivl = $this->interval['1d'] / 2;
        $this->assertEquals($ivl, $card['interval']);
        $this->assertEquals($ease - Settings::$AGAIN_EASE, $card['ease']);
    }

    /** @test */
    public function it_can_calculate_interval_for_easy_after_2_step()
    {
        $ease = 250;
        $card = $this->makeCard(false, $this->interval['1d'], 2, $ease);

        $card = $this->sm2->cardAnswer($card, 'easy');

        $ivl = $this->interval['1d'] / 2;
        $this->assertEquals($ivl, $card['interval']);
        $this->assertEquals($ease - Settings::$AGAIN_EASE, $card['ease']);
    }
}