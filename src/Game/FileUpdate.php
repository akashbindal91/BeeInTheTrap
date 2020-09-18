<?php


namespace Game;

class FileUpdate extends Calculations
{
    private $filepath;
    private $counter_value;
    private $total_hits;
    private $total_hit_count;
    private $bees_value_list;
    
    function __construct()
    {
        $this->counter_value = 0;
        $this->total_hits = 0;
        $this->total_hit_count = 0;
        $this->bees_value_list = [];
        $this->filepath = 'data.json';
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
        $file = $this->file_read();
        if (isset($file['bees_val']) && isset($value['bees_val'])) {
            $type_array = array_column($value['bees_val'], 'type');

            foreach ($file['bees_val'] as &$_value) {
                if (in_array($_value['type'], $type_array)) {
                    $index = array_search($_value['type'], $type_array);
                    $_value = $value['bees_val'][$index];
                } else {
                    $_value['total_hit_points'] = 0;
                }
            }
        }
        $json = json_encode($file);
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
                    $value['total_hit_points'] = $this->get_multiply($value['count'], $value['life']);
                    for ($i = 0; $i < $value['count']; $i++) {
                        array_push($this->bees_value_list, $value['type']);
                    }
                }
            }
        }
        shuffle($this->bees_value_list);

        if (json_encode($temp_data) == json_encode($initial_data)) {
        } else {
            $this->write_in_file($temp_data);
        }

        $initial_data = $this->file_read();
        return $initial_data;
    }

    function alive_bees()
    {

        $initial_data = $this->file_read();
        if (!isset($initial_data)) {
            $this->counter_value = $this->get_addition($this->counter_value, 1);
            if ($this->counter_value == 3) {
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
                        array_splice($temp_data_value, $key, 1);
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
        $temp_hit_value = 0;
        $allow_temp_hit_value = false;

        if (isset($temp_data) && isset($temp_data['bees_val']) && count($temp_data['bees_val']) > 0) {

            if (count($this->bees_value_list) > 1) {
                $random_key = rand(0, $this->get_difference(count($this->bees_value_list), 1));
            } else {
                $random_key = 0;
            }
            $bee_type_array = array_column($temp_data['bees_val'], 'type');
            $random_index = array_search($this->bees_value_list[$random_key], $bee_type_array);

            if (isset($random_index)) {
                if (isset($temp_data['bees_val'][$random_index]['total_hit_points']) && isset($temp_data['bees_val'][$random_index]['hit'])) {

                    $is_not_divisible = $this->get_remainder($temp_data['bees_val'][$random_index]['total_hit_points'], $temp_data['bees_val'][$random_index]['life']);


                    if (!$is_not_divisible) {
                    } else {
                        if ($is_not_divisible < $temp_data['bees_val'][$random_index]['hit']) {
                            $allow_temp_hit_value = true;
                            $temp_hit_value = $is_not_divisible;
                        }
                    }

                    if ($allow_temp_hit_value) {
                        $temp_data['bees_val'][$random_index]['total_hit_points'] = $this->get_difference($temp_data['bees_val'][$random_index]['total_hit_points'], $temp_hit_value);
                        $this->total_hits = $this->get_addition($this->total_hits,  $temp_hit_value);
                    } else {
                        $temp_data['bees_val'][$random_index]['total_hit_points'] = $this->get_difference($temp_data['bees_val'][$random_index]['total_hit_points'], $temp_data['bees_val'][$random_index]['hit']);
                        $this->total_hits = $this->get_addition($this->total_hits,  $temp_data['bees_val'][$random_index]['hit']);
                    }

                    if (($temp_data['bees_val'][$random_index]['type'] == 'queen') && ($temp_data['bees_val'][$random_index]['total_hit_points'] < 1)) {
                        $message = 'Game Over. You have killed the Queen';
                        $this->game_over($message, true);
                    } else {
                        $is_not_divisible = $this->get_remainder($temp_data['bees_val'][$random_index]['total_hit_points'], $temp_data['bees_val'][$random_index]['life']);
                        if (!$is_not_divisible) {

                            $index = array_search($temp_data['bees_val'][$random_index]['type'], $this->bees_value_list);
                            array_splice($this->bees_value_list, $index, 1);

                            $message = 'You have killed a ' . ucfirst($temp_data['bees_val'][$random_index]['type']);
                        } else {
                            $message = 'Direct Hit. You took '. ucfirst($temp_data['bees_val'][$random_index]['hit']) .' hit points from a '.ucfirst($temp_data['bees_val'][$random_index]['type']).' bee';
                        }
                        $this->game_over($message, false);
                    }
                } else {
                    $message = "Some error occured. Please try again";
                    $this->game_over($message, false);
                }
            }
        } else {
            $message = "Game Over. You have killed All the bees";
            $this->game_over($message, true);
        }

        if (json_encode($temp_data) == json_encode($initial_data)) {
        } else {
            $this->write_in_file($temp_data);
        }
        return $temp_data;
    }


    function game_over($message, $status)
    {
        $this->create_set_up();
        echo $message . PHP_EOL;
        if ($status) {
            echo 'It took you ' . $this->total_hits . ' hit points to end the Game.' . PHP_EOL;
            echo 'It took you ' . $this->total_hit_count . ' hits to end the Game.' . PHP_EOL;
            exit();
        }
        return true;
    }

    function play_game()
    {
        $commandLine = readline(('Type hit : '));
        // exit($commandLine);

        if($commandLine !== 'hit') {
            $message = 'Game Aborted. Restart Again.';
            $this->game_over( $message , false);
            exit();
        }
        $this->total_hit_count = $this->get_addition($this->total_hit_count,  1);
        $this->kill_bees();
        $this->play_game();
    }
}
