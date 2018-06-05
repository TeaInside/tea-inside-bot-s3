<?php

// $argv[1] = '{
//     "update_id": 344831842,
//     "message": {
//         "message_id": 5137,
//         "from": {
//             "id": 562623987,
//             "is_bot": false,
//             "first_name": "Vasco da",
//             "last_name": "Gama",
//             "username": "VascoDaGama1460",
//             "language_code": "en-us"
//         },
//         "chat": {
//             "id": -1001128970273,
//             "title": "A",
//             "type": "supergroup"
//         },
//         "date": 1528200353,
//         "text": "/debug",
//         "entities": [
//             {
//                 "offset": 0,
//                 "length": 6,
//                 "type": "bot_command"
//             }
//         ]
//     }
// }';
// $argv[1] = rawurlencode($argv[1]);

if (isset($argv[1])) {
	require __DIR__."/../vendor/autoload.php";
	require __DIR__."/../config/telegram/main.php";
	\Bot\Telegram::run(rawurldecode($argv[1]));
} else {
	print "\n\$argv[1] is not provided!\n";
	exit(1);
}

// debug only
// $data = json_encode(json_decode(rawurldecode($argv[1])), 128 | JSON_UNESCAPED_SLASHES);
// require __DIR__."/debug_telegram.php";
