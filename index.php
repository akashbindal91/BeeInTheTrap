<?php

class FileUpdate
{
    private $filepath;
    private $counter_value;
    private $total_hits;
    function __construct() {
        $this->counter_value = 0;
        $this->total_hits = 0;
        $this->filepath = 'data.json';
    }

    function get_name()
    {
        return $this->name;
    }

    function file_read()
    {
        $myfile = fopen($this->filepath, "r") or die("Unable to open file!");
        $fileData = fread($myfile, filesize($this->filepath));
        fclose($myfile);
        $fileData = json_decode($fileData, true);
        return $fileData;
    }

    function write_in_file($value)
    {
        $json = json_encode($value);
        if (file_put_contents($this->filepath, $json)) {
            return $this->file_read();
        } else {
            $message = "Oops! Error creating json file...";
            $this->game_over($message, true);
        }
    }

    function create_set_up()
    {
        $initial_data = $this->file_read();
        $temp_data = $initial_data;
        foreach ($temp_data as $temp_data_key => &$temp_data_value) {
            if ($temp_data_key == 'bees_val') {
                foreach ($temp_data_value as $key => &$value) {
                    $value['total_hit_points'] = $value['count'] * $value['life'];
                }
            }
        }

        if (json_encode($temp_data) == json_encode($initial_data)) {} else {
            $this->write_in_file($temp_data);
        }

        $initial_data = $this->file_read();
        return $initial_data;
    }

    function alive_bees()
    {

        $initial_data = $this->file_read();
        if(!isset($initial_data)) {
            $this->counter_value = $this->counter_value + 1;
            if($this->counter_value == 3) {
                $message = "its ends here";
                $this->game_over($message, true);
            }
            $this->alive_bees();
        }
        $temp_data = $initial_data;

        foreach ($temp_data as $temp_data_key => &$temp_data_value) {
            if ($temp_data_key == 'bees_val') {
                foreach ($temp_data_value as $key => &$value) {
                    if ($value['total_hit_points'] < 1) {
                        if ($value['type'] == 'queen') {
                            $message = "Queen is dead. Game over";
                            $this->game_over($message, true);
                        }
                        unset($temp_data_value[$key]);
                    }
                }
            }
        }
        
        return $temp_data;
    }

    function kill_bees()
    {
        $initial_data = $this->alive_bees();
        $temp_data = $initial_data;

        if (isset($temp_data) && isset($temp_data['bees_val']) && count($temp_data['bees_val']) > 0) {

            $random_index = rand(0, count($temp_data['bees_val']) - 1);

            if (isset($random_index)) {
                if (isset($temp_data['bees_val'][$random_index]['total_hit_points']) && isset($temp_data['bees_val'][$random_index]['hit'])) {
                    
                    $remainder = $temp_data['bees_val'][$random_index]['total_hit_points'] % $temp_data['bees_val'][$random_index]['hit'];
                    $alternate_value = false;



                    print_r('remainder : '.$remainder);
                    // print_r('remainder : '.$remainder);
                    /* if(isset($remainder) && $remainder < $temp_data['bees_val'][$random_index]['hit']) {
                        $alternate_value = true;
                    } */

                    if ($alternate_value) {
                        $temp_data['bees_val'][$random_index]['total_hit_points'] = $temp_data['bees_val'][$random_index]['total_hit_points'] - $remainder;
                    } else {
                        $temp_data['bees_val'][$random_index]['total_hit_points'] = $temp_data['bees_val'][$random_index]['total_hit_points'] - $temp_data['bees_val'][$random_index]['hit'];

                    }
                    if ($alternate_value) {
                        $this->total_hits += $remainder;
                    } else {
                        $this->total_hits += $temp_data['bees_val'][$random_index]['hit'];
                    }

                    if ( ($temp_data['bees_val'][$random_index]['type'] == 'queen') && ($temp_data['bees_val'][$random_index]['total_hit_points'] < 1)) {
                        $message = 'Game Over. You have killed the Queen';
                        $this->game_over($message, true);
                    } else {
                        $remainder = $temp_data['bees_val'][$random_index]['total_hit_points'] % $temp_data['bees_val'][$random_index]['life'];
                        if (!$remainder) {
                            $message = 'You have killed a '. $temp_data['bees_val'][$random_index]['type'];
                        } else {
                            $message = 'You have hit a '.$temp_data['bees_val'][$random_index]['type'].'. Now its remaining health is '. $remainder;
                        }
                        $this->game_over($message , false);
                    }
                } else {
                    $message = "Some error occured. Please try again";
                    $this->game_over($message, true);
                }
            }
        } else {
            $message = "Some error occured. Please try again";
            $this->game_over($message, true);
        }
        
        if (json_encode($temp_data) == json_encode($initial_data)) {
        } else {
            $this->write_in_file($temp_data);
        }
        return $temp_data;
    }


    function game_over($message , $status) {
        $this->create_set_up();
        echo $message.PHP_EOL;
        if ($status) {
            echo 'It took you '. $this->total_hits. ' to end the Game.' .PHP_EOL;
            exit();
        }
        return true;
    }

    function play_game() {
        $commandLine = readline(('Hit Enter'));
        $this->kill_bees();
        $this->play_game();
    }
}

$a = new FileUpdate();
$a->create_set_up();
$a->play_game();
