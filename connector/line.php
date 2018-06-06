<?php


// $argv[1] = '{
//     "events": [
//         {
//             "type": "message",
//             "replyToken": "b424dd70116e4ebdb26e611bb8a30980",
//             "source": {
//                 "groupId": "Ce20228a1f1f98e6cf9d6f6338603e962",
//                 "userId": "Ue051b0e302e7362709e8762895c066eb",
//                 "type": "group"
//             },
//             "timestamp": 1528283710780,
//             "message": {
//                 "type": "text",
//                 "id": "8074656171338",
//                 "text": "@Ammar F. "
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