<?php

namespace GameTextBot\Test;

//  require '../vendor/autoload.php';
use GameTextBot\Log;
use PHPUnit\Framework\TestCase;
use GameTextBot\Quest1;
use GameTextBot\RandomQuest;

class FlowTest extends TestCase
{
    public function testQuest1()
    {
        $log = Log::get('FlowTest');
        $log->debug("FlowTest start");

        $quest = new Quest1(123);
        $quest->Reset();
        echo ($quest->GetText());
        echo "\n";

        $quest->LoadCurrentState();
        $quest->CheckCurrentState("moveto_OgcykvqoKtf-7uEIAw74-5");
        echo ($quest->GetText());
        $this->assertSame($quest->GetText(), "Локация 2");
        echo "\n";

        $quest->LoadCurrentState();
        $quest->CheckCurrentState("moveto_OgcykvqoKtf-7uEIAw74-2");
        echo ($quest->GetText());
        $this->assertSame($quest->GetText(), "Локация 2");
        echo "\n";

        $quest->LoadCurrentState();
        $quest->CheckCurrentState("moveto_OgcykvqoKtf-7uEIAw74-11");
        echo ($quest->GetText());
        $this->assertSame($quest->GetText(), "Локация 4");
        echo "\n";

        $quest->LoadCurrentState();
        $quest->CheckCurrentState("moveto_OgcykvqoKtf-7uEIAw74-7");
        echo ($quest->GetText());
        $this->assertSame($quest->GetText(), "Локация 3");
        echo "\n";

        $log->debug("FlowTest stop");
    }

    public function testBot_php_load()
    {
        try {
            include './bot.php';
        } catch (Exception $e) {
            echo $e->getMessage();
            //"Connection timed out after
            //SSL connection timeout
            //SSL certificate problem: self signed certificate in certificate chain
            //$this->assertContains()
            //$this->assertEmpty($e->getMessage());
        }
    }

    public function testRandomQuest()
    {
        $log = Log::get('FlowTest');
        $log->debug("testRandomQuest start");

        $quest = new RandomQuest(123);
        $quest->Reset();
        echo ($quest->GetText());
        echo "\n";
        var_dump($quest->get_inline_keyboard());
        echo "\n";
        $this->assertEquals("Выбор", $quest->GetText());

        for ($i = 0; $i < 5; $i++) {

            $quest->LoadCurrentState();
            $quest->CheckCurrentState("moveto_hgMCD8Qc4Z5fNGhsjrsD-2_random");
            echo ($quest->GetText());
            echo "\n";
            $this->assertTrue(in_array($quest->GetText(), ["1", "2", "3"]));

            $quest->LoadCurrentState();
            $quest->CheckCurrentState("moveto_hgMCD8Qc4Z5fNGhsjrsD-2_random");
            echo ($quest->GetText());
            echo "\n";
            $this->assertTrue(in_array($quest->GetText(), ["1", "2", "3"]));

            $quest->LoadCurrentState();
            $quest->CheckCurrentState("moveto_hgMCD8Qc4Z5fNGhsjrsD-2");
            echo ($quest->GetText());
            echo "\n";
            $this->assertEquals("Выбор", $quest->GetText());

            $quest->LoadCurrentState();
            $quest->CheckCurrentState("moveto_hgMCD8Qc4Z5fNGhsjrsD-2");
            echo ($quest->GetText());
            echo "\n";
            $this->assertEquals("Выбор", $quest->GetText());
        }
    }
}
