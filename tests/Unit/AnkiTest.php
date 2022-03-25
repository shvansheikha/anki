<?php

namespace Jackwestin\AnkiSandbox\Tests\Unit;

use Jackwestin\AnkiSandbox\app\Service\SM2;
use PHPUnit\Framework\TestCase;

class AnkiTest extends TestCase
{
    private $sm2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sm2= new SM2();
    }

    private function makeCard($new = true, $ease = 0, $interval = 0, $repeat = 0)
    {
        return [
            "id" => 1,
            "title" => "card 1",
            "answer" => "",
            "new" => $new,
            "ease" => $ease,
            "interval" => $interval,
            "repeat" => $repeat
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
}