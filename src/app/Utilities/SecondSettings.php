<?php

namespace Jackwestin\AnkiSandbox\app\Utilities;

class SecondSettings
{
//    public static float $AGAIN_EASE = 0.20;
//    public static float $HARD_EASE = 1.2;
//    public static float $EASY_EASE = 0.15;
//    public static float $HARD_SUB_EASE = 0.15;
//
//    public static float $DEFAULT_EASY_BONUS = 1.3;
//    public static float $DEFAULT_STARTING_EASE = 2.5;
//
//    public static float $INTERVAL_MODIFIER = 1.0;
//    public static float $MINIMUM_EASE = 1.3;
//
//    public static array $FIRST_STEP_INTERVAL = ['again' => 60, 'hard' => 360, 'good' => 600, 'easy' => 345600];
//    public static array $SECOND_STEP_INTERVAL = ['again' => 60, 'hard' => 600, 'good' => 86400, 'easy' => 345600];
//
    public static array $VALID_ANSWER = ['again', 'hard', 'good', 'easy'];
//
//    public static array $LEARNING_STEPS = ['again' => 0, 'hard' => 0, 'good' => 1, 'easy' => 2];

    public static $NEW_STEPS = [60, 600];
    public static $GRADUATING_INTERVAL = 86400;
    public static $EASY_INTERVAL = 345600;
    public static $STARTING_EASE = 250;

# "Reviews" tab
    public static $EASY_BONUS = 130;
    public static $INTERVAL_MODIFIER = 100;
    public static $MAXIMUM_INTERVAL = 36500;

# "Lapses" tab
    public static $LAPSES_STEPS = [600];
    public static $NEW_INTERVAL = 70;
    public static $MINIMUM_INTERVAL = 1;
}