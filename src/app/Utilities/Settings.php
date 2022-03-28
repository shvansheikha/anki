<?php

namespace Jackwestin\AnkiSandbox\app\Utilities;

class Settings
{
    public static $AGAIN_EASE = 0.20;
    public static $HARD_EASE = 1.2;
    public static $EASY_EASE = 0.15;
    public static $HARD_SUB_EASE = 0.15;

    public static $DEFAULT_EASY_BONUS = 1.3;
    public static $DEFAULT_STARTING_EASE = 2.5;

    public static $INTERVAL_MODIFIER = 1;
    public static $MINIMUM_EASE = 1.3;

    public static $FIRST_STEP_INTERVAL = ['again' => 60, 'hard' => 360, 'good' => 600, 'easy' => 345600];
    public static $SECOND_STEP_INTERVAL = ['again' => 60, 'hard' => 600, 'good' => 86400, 'easy' => 345600];
}