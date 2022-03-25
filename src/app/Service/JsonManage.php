<?php

namespace Jackwestin\AnkiSandbox\app\Service;

class JsonManage
{
    private $CARD_URL;

    public function __construct()
    {
        $this->CARD_URL = __DIR__ . '/../Database/cards.json';
    }

    public function getCards()
    {
        $string = file_get_contents($this->CARD_URL);

        return json_decode($string, TRUE);
    }

    public function saveJson($data)
    {
        file_put_contents($this->CARD_URL, json_encode($data));
    }
}