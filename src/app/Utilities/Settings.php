<?php

namespace Jackwestin\AnkiSandbox\app\Utilities;

class Settings
{
    public static $AGAIN_EASE = 20;
    public static $HARD_EASE = 120;
    public static $EASY_EASE = 15;

    public static $DEFAULT_EASY_BONUS = 130;
    public static $DEFAULT_STARTING_EASE = 250;

    public static $FIRST_STEP_INTERVAL = ['again' => 60, 'hard' => 360, 'good' => 600, 'easy' => 345600];
    public static $SECOND_STEP_INTERVAL = ['again' => 60, 'hard' => 600, 'good' => 86400, 'easy' => 345600];
}