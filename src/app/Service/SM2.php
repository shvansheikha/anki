<?php

namespace Jackwestin\AnkiSandbox\app\Service;

use Jackwestin\AnkiSandbox\app\Utilities\Settings;

class SM2
{
    public function cardAnswer($card, $answer)
    {
        $step = $card['step'];

        if ($this->isFirstStep($card)) {

            $newInterval = Settings::$FIRST_STEP_INTERVAL[$answer];
            $newEase = Settings::$DEFAULT_STARTING_EASE;

            if ($answer == "good") {
                $step = 1;
            }

            if ($answer == "easy") {
                $step = 2;
            }

        } elseif ($this->isSecondStep($card['step'])) {
            $newInterval = Settings::$SECOND_STEP_INTERVAL[$answer];
            $newEase = $card['ease'];

            if (in_array($answer, ['good', 'easy'])) {
                $step++;
            }
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
                $step--;
                break;
            case "hard":
                $newInterval = $currentInterval * (Settings::$HARD_EASE / 100) * 1;
                $newEase = $currentEase - 15;
                break;
            case "good":
                $newInterval = $currentInterval * ($currentEase / 100) * 1;
                $newEase = $currentEase;
                $step++;
                break;
            case "easy":
                $newInterval = $currentInterval * ($currentEase / 100) * 1 * (Settings::$DEFAULT_EASY_BONUS / 100);
                $newEase = $currentEase + Settings::$EASY_EASE;
                $step++;
                break;
            default:
                $newInterval = 0;
                $newEase = 0;
                break;
        }

        $newEase = $newEase < 130 ? 130 : $newEase; // should not fall below the value of 1.3
        return [$newInterval, $newEase, $step];
    }

    private function isFirstStep($card): bool
    {
        return $card['new'] || ($card['step'] == 0);
    }

    private function isSecondStep($step): bool
    {
        return $step == 1;
    }
}