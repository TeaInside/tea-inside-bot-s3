<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config/telegram/main.php";

$a = \Bot\Telegram\Exe::{$argv[1]}(...json_decode(rawurldecode($argv[2]), true));
echo $a["out"]."\n\n";
