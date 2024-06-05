<?php

namespace Shvan\AnkiSandbox\app\Service;

use ErrorException;
use Shvan\AnkiSandbox\app\Enums\CardStatus;
use Shvan\AnkiSandbox\app\Utilities\SecondSettings;
use Shvan\AnkiSandbox\app\Utilities\Settings;

class SSM2
{
    public function cardAnswer($card, $answer)
    {
        if (!in_array($answer, SecondSettings::$VALID_ANSWER)) {
            throw new ErrorException("answer is not valid.");
        }

        $step = $card->step;
        switch ($card->status) {
            case CardStatus::LEARNING:
                list($step, $status, $interval) = $this->learningCalculations(
                    $card,
                    $answer
                );
                break;

            case CardStatus::LEARNED:
                $this->learnedCalculations($card, $answer);
                break;

            case CardStatus::RELEARNING:
                list($interval, $newEase, $step) = $this->calculate(
                    $card,
                    $answer
                );
                break;
            default:
                throw new ErrorException("status is not valid!");
        }

        $card->new = false;
        $card->interval = round($interval);
        $card->ease = $newEase;
        $card->step = $step;
        $card->status = $status;

        return $card;
    }

    private function learningCalculations($card, $answer): array
    {
        $step = $card->step;
        $status = $card->status;
        $interval = $card->interval;

        switch ($answer) {
            case "again":
                $step = 0;
                $card->interval = SecondSettings::$NEW_STEPS[$step];
                break;
            case "hard":
                $step = 0;
                $card->interval = 360;
                break;
            case "good":
                $step += 1;
                if ($step < count(SecondSettings::$NEW_STEPS)) {
                    $interval = SecondSettings::$NEW_STEPS[$step];
                } else {
                    $status = CardStatus::LEARNED;
                    $interval = SecondSettings::$GRADUATING_INTERVAL;
                }
                break;
            case "easy":
                $status = CardStatus::LEARNED;
                $interval = SecondSettings::$EASY_INTERVAL;
                break;
        }

        return [$step, $status, $interval];
    }

    private function learnedCalculations($card, $answer)
    {
        $step = $card->step;
        $status = $card->status;
        $interval = $card->interval;
        $ease = $card->ease;

        switch ($answer) {
            case "again":
                $status = CardStatus::RELEARNING;
                $step = 0;
                $ease = max(130, $ease - 20);
                $interval = max(
                    SecondSettings::$MINIMUM_INTERVAL,
                    ($interval * SecondSettings::$NEW_INTERVAL) / 100
                );
                return SecondSettings::$LAPSES_STEPS[0];
                break;
            case "hard":
                $ease = max(130, $ease - 15);
                $interval =
                    ($interval * 1.2 * SecondSettings::$INTERVAL_MODIFIER) /
                    100;
                $interval = min(SecondSettings::$MAXIMUM_INTERVAL, $interval);
                break;
            case "good":
                $interval =
                    ((($interval * $ease) / 100) *
                        SecondSettings::$INTERVAL_MODIFIER) /
                    100;
                $interval = min(SecondSettings::$MAXIMUM_INTERVAL, $interval);
                break;
            case "easy":
                $ease += 15;
                $interval =
                    ((((($interval * $ease) / 100) *
                        SecondSettings::$INTERVAL_MODIFIER) /
                        100) *
                        SecondSettings::$EASY_BONUS) /
                    100;
                $interval = min(SecondSettings::$MAXIMUM_INTERVAL, $interval);
                break;
        }

        return [$step, $status, $interval];
    }

    private function calculate($card, $answer): array
    {
        $currentInterval = $card->interval;
        $currentEase = $card->ease;
        $step = $card->step;

        switch ($answer) {
            case "again":
                $newInterval = $currentInterval / 2;
                $newEase = $currentEase - Settings::$AGAIN_EASE;
                $step--;
                break;
            case "hard":
                $newInterval =
                    $currentInterval *
                    Settings::$HARD_EASE *
                    Settings::$INTERVAL_MODIFIER;
                $newEase = $currentEase - Settings::$HARD_SUB_EASE;
                break;
            case "good":
                $newInterval =
                    $currentInterval *
                    $currentEase *
                    Settings::$INTERVAL_MODIFIER;
                $newEase = $currentEase;
                $step++;
                break;
            case "easy":
                $newInterval =
                    $currentInterval *
                    $currentEase *
                    Settings::$INTERVAL_MODIFIER *
                    Settings::$DEFAULT_EASY_BONUS;
                $newEase = $currentEase + Settings::$EASY_EASE;
                $step++;
                break;
            default:
                $newInterval = 0;
                $newEase = 0;
                break;
        }

        $newEase =
            $newEase < Settings::$MINIMUM_EASE
                ? Settings::$MINIMUM_EASE
                : $newEase;
        return [$newInterval, $newEase, $step];
    }
}
