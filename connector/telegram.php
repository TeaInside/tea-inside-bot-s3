<?php

$argv[1] = '{
    "update_id": 69829664,
    "message": {
        "message_id": 5312,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F.",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001134449138,
            "title": "Private Cloud",
            "type": "supergroup"
        },
        "date": 1528304718,
        "photo": [
            {
                "file_id": "AgADBQADDKgxG0oMwFTI47SINqneI4Iz1TIABIvHOhnkhJOD_hwBAAEC",
                "file_size": 1183,
                "file_path": "photos/file_69.jpg",
                "width": 90,
                "height": 56
            },
            {
                "file_id": "AgADBQADDKgxG0oMwFTI47SINqneI4Iz1TIABAaxwteLtROc_xwBAAEC",
                "file_size": 15994,
                "width": 320,
                "height": 200
            },
            {
                "file_id": "AgADBQADDKgxG0oMwFTI47SINqneI4Iz1TIABF1X6mwK-RXdAAEdAQABAg",
                "file_size": 78491,
                "width": 800,
                "height": 500
            },
            {
                "file_id": "AgADBQADDKgxG0oMwFTI47SINqneI4Iz1TIABJ5Kpa-YH9aFAR0BAAEC",
                "file_size": 179215,
                "width": 1280,
                "height": 800
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