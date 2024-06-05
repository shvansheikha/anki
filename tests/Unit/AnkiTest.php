<?php

namespace Shvan\AnkiSandbox\Tests\Unit;

use Shvan\AnkiSandbox\app\Enums\CardStatus;
use Shvan\AnkiSandbox\app\Service\SM2;
use Shvan\AnkiSandbox\app\Utilities\Settings;
use PHPUnit\Framework\TestCase;
use stdClass;

class AnkiTest extends TestCase
{
    private SM2 $sm2;

    private array $interval = [
        "1m" => 60,
        "6m" => 360,
        "10m" => 600,
        "1d" => 86400,
        "4d" => 345600,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->sm2 = new SM2();
    }

    private function makeCard(
        $new = true,
        $interval = 0,
        $step = 0,
        $ease = 0,
        $status = CardStatus::LEARNING
    ): stdClass {
        $card = new stdClass();

        $card->id = 1;
        $card->title = "card 1";
        $card->answer = "";
        $card->new = $new;
        $card->ease = $ease;
        $card->interval = $interval;
        $card->step = $step;
        $card->status = $status;

        return $card;
    }

    /** @test */
    public function it_can_change_new_column()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "again");

        $this->assertFalse($card->new);
    }

    /** @test */
    public function it_can_update_step_for_new_card_if_answer_was_good()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "good");

        $this->assertEquals($card->step, 1);
    }

    /** @test */
    public function it_can_update_step_for_new_card_if_answer_was_easy()
    {
        $card = $this->makeCard();

        $card = $this->sm2->cardAnswer($card, "easy");

        $this->assertEquals($card->step, 2);
    }

    /** @test */
    public function it_can_update_step_after_three_time()
    {
        $card = $this->makeCard(
            false,
            $this->interval["1d"],
            2,
            Settings::$DEFAULT_STARTING_EASE
        );

        $card = $this->sm2->cardAnswer($card, "easy");

        $this->assertEquals($card->step, 3);
    }

    /** @test */
    public function it_can_calculate_interval_for_again_after_2_step()
    {
        $ease = Settings::$DEFAULT_STARTING_EASE;
        $card = $this->makeCard(false, $this->interval["1d"], 2, $ease);

        $card = $this->sm2->cardAnswer($card, "again");

        $ivl = $this->interval["1d"] / 2;
        $this->assertEquals($ivl, $card->interval);
        $this->assertEquals($ease - Settings::$AGAIN_EASE, $card->ease);
    }

    /** @test */
    public function it_can_calculate_interval_for_hard_after_2_step()
    {
        $ease = Settings::$DEFAULT_STARTING_EASE;
        $card = $this->makeCard(false, $this->interval["1d"], 2, $ease);

        $card = $this->sm2->cardAnswer($card, "hard");

        $ivl =
            $this->interval["1d"] *
            Settings::$HARD_EASE *
            Settings::$INTERVAL_MODIFIER;
        $this->assertEquals($ivl, $card->interval);
        $this->assertEquals(2, $card->step);
        $this->assertEquals($ease - Settings::$HARD_SUB_EASE, $card->ease);
    }

    /** @test */
    public function it_can_calculate_interval_for_good_after_2_step()
    {
        $ease = Settings::$DEFAULT_STARTING_EASE;
        $card = $this->makeCard(false, $this->interval["1d"], 2, $ease);

        $card = $this->sm2->cardAnswer($card, "good");

        $ivl = $this->interval["1d"] * $ease * Settings::$INTERVAL_MODIFIER;
        $this->assertEquals($ivl, $card->interval);
        $this->assertEquals(3, $card->step);
        $this->assertEquals($ease, $card->ease);
    }

    /** @test */
    public function it_can_calculate_interval_for_easy_after_2_step()
    {
        $ease = Settings::$DEFAULT_STARTING_EASE;
        $card = $this->makeCard(false, $this->interval["1d"], 2, $ease);

        $card = $this->sm2->cardAnswer($card, "easy");

        $ivl =
            $this->interval["1d"] *
            $ease *
            Settings::$INTERVAL_MODIFIER *
            Settings::$DEFAULT_EASY_BONUS;
        $this->assertEquals($ivl, $card->interval);
        $this->assertEquals(3, $card->step);
        $this->assertEquals($ease + Settings::$EASY_EASE, $card->ease);
    }
}
