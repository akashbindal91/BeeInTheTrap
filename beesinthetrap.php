<?php

use Game\FileUpdate;

require 'vendor/autoload.php';


$game = new Game\FileUpdate();
$game->create_set_up();
$game->play_game();
