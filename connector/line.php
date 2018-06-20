<?php

if (isset($argv[1])) {
    require __DIR__."/../vendor/autoload.php";
    require __DIR__."/../config/line/main.php";
    \Bot\Line::run(rawurldecode($argv[1]));
} else {
	print "\n\$argv[1] is not provided!\n";
	exit(1);
}


// debug only
$data = json_encode(json_decode(rawurldecode($argv[1])), 128 | JSON_UNESCAPED_SLASHES);
require __DIR__."/debug_line.php";
