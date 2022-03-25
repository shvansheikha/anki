<?php

namespace Jackwestin\AnkiSandbox\app\Service;

class SM2
{
    public function cardAnswer($card, $answer)
    {
        $defaultStartingEase = 250;
        $repeat = 0;

        if ($card['new']) {
            $learnInterval = ['again' => 60, 'hard' => 360, 'good' => 600, 'easy' => 345600];
            $newInterval = $learnInterval[$answer];
            $newEase = $defaultStartingEase;

            if ($answer == "good") {
                $repeat = 1;
            }

            if ($answer == "easy") {
                $repeat = 2;
            }

        } elseif ($card['repeat'] == 1) {
            $learnInterval = ['again' => 60, 'hard' => 600, 'good' => 86400, 'easy' => 345600];
            $newInterval = $learnInterval[$answer];
            $newEase = $card['ease'];
        } else {
            list($newInterval, $newEase, $repeat) = $this->calculate($card, $answer);
        }

        $card['new'] = false;

        return [$card, $answer, $newInterval, $newEase, $repeat];
    }

    private function calculate($card, $answer)
    {
        $hardEase = 1.2;
        $defaultEasyBonus = 1.3;

        $currentInterval = $card['interval'];
        $currentEase = $card['ease'];
        $repeat = $card['repeat'];

        switch ($answer) {
            case "again":
                $newInterval = $currentInterval % 2;
                $newEase = $currentEase - 20;
                break;
            case "hard":
                $newInterval = $currentInterval * $hardEase * 1;
                $newEase = $currentEase - 15;
                break;
            case "good":
                $newInterval = $currentInterval * $currentEase * 1;
                $newEase = $currentEase;
                $repeat++;
                break;
            case "easy":
                $newInterval = $currentInterval * $currentEase * 1 * $defaultEasyBonus;
                $newEase = $currentEase + 15;
                $repeat++;
                break;
            default:
                $newInterval = 0;
                $newEase = 0;
                break;
        }

        return [$newInterval, $newEase, $repeat];
    }
}