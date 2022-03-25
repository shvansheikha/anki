<?php

namespace Jackwestin\AnkiSandbox\Tests\Unit;

use Jackwestin\AnkiSandbox\app\Service\SM2;
use PHPUnit\Framework\TestCase;

class AnkiTest extends TestCase
{
    /** @test */
    public function it_can_change_new_column()
    {
        $sm2 = new SM2();

        $card = [
            "id" => 1,
            "title" => "card 1",
            "answer" => "",
            "previous" => 0,
            "new" => true,
            "ease" => 0,
            "interval" => 0,
            "repeat" => 0
        ];

       list($card) = $sm2->cardAnswer($card, "good");

       $this->assertFalse($card['new']);
    }
}