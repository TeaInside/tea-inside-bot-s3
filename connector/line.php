<?php


// $argv[1] = '{
//     "events": [
//         {
//             "type": "message",
//             "replyToken": "3293df8625b14a3a930172f0fa47ba47",
//             "source": {
//                 "userId": "U547ba62dc793c6557abbb42ab347f15f",
//                 "type": "user"
//             },
//             "timestamp": 1528279788347,
//             "message": {
//                 "type": "text",
//                 "id": "8074345942053",
//                 "text": "/tr en id good morning"
//             }
//         }
//     ]
// }';

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