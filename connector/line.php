<?php



// debug only
$data = json_encode(json_decode(rawurldecode($argv[1])), 128 | JSON_UNESCAPED_SLASHES);
require __DIR__."/debug_line.php";