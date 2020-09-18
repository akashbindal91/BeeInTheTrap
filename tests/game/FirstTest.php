<?php

use Game\Calculations;
use Game\FileUpdate;
use PHPUnit\Framework\TestCase;
// use Calculations;
class FirstTest extends TestCase  {
    
    function test_multiply() {
        $a = 5; $b = 10;
        $c = Calculations::get_multiply($a, $b);

        $this->assertEquals($c , 50);
    }

    function test_divide() {
        $a = 10; $b = 2;
        $c = Calculations::get_dividend($a, $b);

        $this->assertEquals($c , 5);
    }

    function test_add() {
        $a = 5; $b = 10;
        $c = Calculations::get_addition($a, $b);

        $this->assertEquals($c , 15);
    }

    function test_subtract() {
        $a = 15; $b = 10;
        $c = Calculations::get_difference($a, $b);

        $this->assertEquals($c , 5);
    }

    function test_remainder() {
        $a = 15; $b = 10;
        $c = Calculations::get_remainder($a, $b);

        $this->assertEquals($c , 5);
    }

    function test_file_exists() {
        $file = $this->assertFileExists('./test_json_1.json');
    }
    
    function test_file_is_readable() {
        $file = $this->assertFileIsReadable('./test_json_1.json');
    }    
    
    function test_file_is_writable() {
        $file = $this->assertFileIsWritable('./test_json_1.json');
    }
    
    function test_file_data_is_array() {
        $fileData = $this->read_file();
        $this->assertIsArray($fileData);
    }
    
    function test_file_has_key_bees_val() {
        $fileData = $this->read_file();
        $this->assertArrayHasKey('bees_val', $fileData);
    }
    
    function read_file() {
        $myfile = fopen('./test_json_1.json', "r") or die("Unable to open file!");
        $fileData = fread($myfile, filesize('./test_json_1.json'));
        fclose($myfile);
        return json_decode($fileData, true);
    }
}