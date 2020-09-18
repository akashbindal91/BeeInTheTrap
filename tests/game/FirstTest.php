<?php

require '../../calculations.php';

use PHPUnit\Framework\TestCase;
use Calculations;
class FirstTest extends TestCase {

    /* private $calculations;
    function __construct() {

        $this->calculations = new Calculations;
    
    } */
    
    function test_one() {
        $calculations = new Calculations;
        $a = 5; $b = 10;
        $c = $calculations->get_multiply($a, $b);

        $this->assertEquals($c , 50);


    }
}