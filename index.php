<?php

use Jackwestin\AnkiSandbox\app\Console\AnkiCommand;
use Jackwestin\AnkiSandbox\app\Console\AnkiCommandTwo;
use Symfony\Component\Console\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->add(new AnkiCommand());
$app->add(new AnkiCommandTwo());

$app->run();
?>