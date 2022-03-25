<?php

namespace Jackwestin\AnkiSandbox\app\Console;

use Jackwestin\AnkiSandbox\app\Service\JsonManage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AnkiCommandTwo extends Command
{
    protected static $defaultName = "anki2:card";

    private $data;

    private $jsonManage;

    public function __construct()
    {
        parent::__construct(self::$defaultName);

        $this->jsonManage = new JsonManage();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->data = $this->jsonManage->getCards();

        $helper = $this->getHelper("question");

        foreach ($this->data as $card) {
            $answer = $this->showCard($card, $helper, $input, $output);

            $this->saveAnswer($card, $answer);

//            $this->printInfo($output);
        }

        $this->printList($output);

//        $this->resetData();

        return Command::SUCCESS;
    }

    private function showCard($card, $helper, $input, OutputInterface $output)
    {
        $question = new ChoiceQuestion(
            $card['title'],
            array('again', 'hard', 'good', 'easy'),
            0
        );

        $question->setErrorMessage('Answer %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function saveAnswer($card, $answer)
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

        foreach ($this->data as $key => $entry) {
            if ($entry['id'] == $card['id']) {

                $this->data[$key]['answer'] = $answer;
                $this->data[$key]['new'] = false;
                $this->data[$key]['interval'] = round($newInterval);
                $this->data[$key]['ease'] = $newEase;
                $this->data[$key]['repeat'] = $repeat;

                $this->jsonManage->saveJson($this->data);
                break;
            }
        }
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

    protected function hasCard()
    {
        $answers = array_column($this->data, 'answer');

        return
            in_array("again", $answers) ||
            in_array("hard", $answers) ||
            in_array("good", $answers);
    }

    private function printInfo(OutputInterface $output)
    {
        $new = array_filter($this->data, function ($object) use ($output) {
            return $object['new'];
        });

        $review = array_filter($this->data, function ($object) use ($output) {
            return ($object['new'] == false) &&
                (in_array($object['answer'], ['again', 'hard'])
                    ||
                    ($object['answer'] == "good" && $object['interval'] == 600)
                );
        });

        $output->writeln('new = ' . count($new) . ' review = ' . count($review));
    }

    private function printList(OutputInterface $output)
    {
        foreach ($this->data as $card) {
            $output->writeln(
                'id = ' . $card['id']
                . '     title = ' . $card['title']
                . '     interval = ' . $card['interval']
                . '     ease = ' . $card['ease']
            );
        }
    }

    private function resetData()
    {
        foreach ($this->data as $key => $entry) {
            $this->data[$key]['answer'] = "again";
            $this->data[$key]['new'] = true;
            $this->data[$key]['interval'] = 0;
            $this->data[$key]['ease'] = 0;
            $this->data[$key]['repeat'] = 0;

            $this->jsonManage->saveJson($this->data);
        }
    }
}