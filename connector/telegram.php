<?php

// $argv[1] = '{
//     "update_id": 344806834,
//     "message": {
//         "message_id": 57401,
//         "from": {
//             "id": 243692601,
//             "is_bot": false,
//             "first_name": "Ammar",
//             "last_name": "F.",
//             "username": "ammarfaizi2",
//             "language_code": "en-US"
//         },
//         "chat": {
//             "id": 243692601,
//             "first_name": "Ammar",
//             "last_name": "F.",
//             "username": "ammarfaizi2",
//             "type": "private"
//         },
//         "date": 1525455505,
//         "text": "/sh echo 123123",
//         "entities": [
//             {
//                 "offset": 0,
//                 "length": 6,
//                 "type": "bot_command"
//             }
//         ]
//     }
// }';

if (isset($argv[1])) {
	require __DIR__."/../vendor/autoload.php";
	require __DIR__."/../config/telegram/main.php";
	\Bot\Telegram::run(rawurldecode($argv[1]));
} else {
	print "\n\$argv[1] is not provided!\n";
	exit(1);
}
