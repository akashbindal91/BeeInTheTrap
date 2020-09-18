<?php

require 'vendor/autoload.php';
require 'src/Game/FileUpdate.php';

$a = new Game\FileUpdate();
$a->create_set_up();
$a->play_game();