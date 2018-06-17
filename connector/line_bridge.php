<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config/line/main.php";

$a = \Bot\Line\Exe::{$argv[1]}(...json_decode(rawurldecode($argv[2]), true));
echo $a["content"]."\n\n";
