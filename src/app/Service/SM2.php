<?php

namespace Jackwestin\AnkiSandbox\app\Service;

use Jackwestin\AnkiSandbox\app\Utilities\Settings;

class SM2
{
    public function cardAnswer($card, $answer)
    {
        $step = 0;

        if ($card['new']) {
            $learnInterval = ['again' => 60, 'hard' => 360, 'good' => 600, 'easy' => 345600];
            $newInterval = $learnInterval[$answer];
            $newEase = Settings::$DEFAULT_STARTING_EASE;

            if ($answer == "good") {
                $step = 1;
            }

            if ($answer == "easy") {
                $step = 2;
            }

        } elseif ($card['step'] == 1) {
            $learnInterval = ['again' => 60, 'hard' => 600, 'good' => 86400, 'easy' => 345600];
            $newInterval = $learnInterval[$answer];
            $newEase = $card['ease'];
        } else {
            list($newInterval, $newEase, $step) = $this->calculate($card, $answer);
        }

        $card['new'] = false;
        $card['interval'] = $newInterval;
        $card['ease'] = $newEase;
        $card['step'] = $step;

        return $card;
    }

    private function calculate($card, $answer): array
    {
        $currentInterval = $card['interval'];
        $currentEase = $card['ease'];
        $step = $card['step'];

        switch ($answer) {
            case "again":
                $newInterval = $currentInterval / 2;
                $newEase = $currentEase - Settings::$AGAIN_EASE;
                break;
            case "hard":
                $newInterval = $currentInterval * Settings::$HARD_EASE * 1;
                $newEase = $currentEase - 15;
                break;
            case "good":
                $newInterval = $currentInterval * $currentEase * 1;
                $newEase = $currentEase;
                $step++;
                break;
            case "easy":
                $newInterval = $currentInterval * $currentEase * 1 * Settings::$DEFAULT_EASY_BONUS;
                $newEase = $currentEase + Settings::$EASY_EASE;
                $step++;
                break;
            default:
                $newInterval = 0;
                $newEase = 0;
                break;
        }

        return [$newInterval, $newEase, $step];
    }
}