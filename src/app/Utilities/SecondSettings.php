<?php

namespace Shvan\AnkiSandbox\app\Utilities;

class SecondSettings
{
    public static array $VALID_ANSWER = ["again", "hard", "good", "easy"];

    public static array $NEW_STEPS = [60, 600];
    public static int $GRADUATING_INTERVAL = 86400;
    public static int $EASY_INTERVAL = 345600;
    public static int $STARTING_EASE = 250;

    # "Reviews" tab
    public static int $EASY_BONUS = 130;
    public static int $INTERVAL_MODIFIER = 100;
    public static int $MAXIMUM_INTERVAL = 36500;

    # "Lapses" tab
    public static array $LAPSES_STEPS = [600];
    public static int $NEW_INTERVAL = 70;
    public static int $MINIMUM_INTERVAL = 1;
}
