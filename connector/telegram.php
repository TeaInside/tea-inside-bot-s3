<?php

// $argv[1] = '{
//     "update_id": 344827803,
//     "message": {
//         "message_id": 58901,
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
//         "date": 1527954473,
//         "reply_to_message": {
//             "message_id": 58900,
//             "from": {
//                 "id": 243692601,
//                 "is_bot": false,
//                 "first_name": "Ammar",
//                 "last_name": "F.",
//                 "username": "ammarfaizi2",
//                 "language_code": "en-US"
//             },
//             "chat": {
//                 "id": 243692601,
//                 "first_name": "Ammar",
//                 "last_name": "F.",
//                 "username": "ammarfaizi2",
//                 "type": "private"
//             },
//             "date": 1527954468,
//             "forward_from": {
//                 "id": 198245216,
//                 "is_bot": false,
//                 "first_name": "Septian",
//                 "last_name": "Hari",
//                 "username": "liqrgv",
//                 "language_code": "en-us"
//             },
//             "forward_date": 1527952001,
//             "text": "Kayak samsung?"
//         },
//         "text": "zxczxc",
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
$data = json_encode(json_decode(rawurldecode($argv[1])), 128 | JSON_UNESCAPED_SLASHES);
require __DIR__."/debug_telegram.php";
