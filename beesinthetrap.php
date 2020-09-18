<?php

use Game\FileUpdate;

require 'vendor/autoload.php';


$a = new Game\FileUpdate();
$a->create_set_up();
$a->play_game();
