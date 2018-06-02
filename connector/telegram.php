<?php

$argv[1] = '{
    "update_id": 69827621,
    "message": {
        "message_id": 4524,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F.",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001128970273,
            "title": "Testing Env",
            "type": "supergroup"
        },
        "date": 1527959235,
        "text": "aaaa",
        "entities": [
            {
                "offset": 0,
                "length": 3,
                "type": "bot_command"
            }
        ]
    }
}';
$argv[1] = rawurlencode($argv[1]);

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
