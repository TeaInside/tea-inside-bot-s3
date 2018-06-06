<?php


$argv[1] = '{
    "events": [
        {
            "type": "message",
            "replyToken": "06a98eb295944419914a93106f764c9d",
            "source": {
                "groupId": "Ce20228a1f1f98e6cf9d6f6338603e962",
                "userId": "U547ba62dc793c6557abbb42ab347f15f",
                "type": "group"
            },
            "timestamp": 1528302841417,
            "message": {
                "type": "image",
                "id": "8076192883200"
            }
        }
    ]
}';

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