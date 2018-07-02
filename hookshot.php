<?php

$a = '{
    "update_id": 344895242,
    "message": {
        "message_id": 9395,
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
            "title": "Private Cloud",
            "type": "supergroup"
        },
        "date": 1530514184,
        "photo": [
            {
                "file_id": "AgADBQADHrExG-zz0VV6saCUHRVV3fZN1TIABHfEZJKkQGwp76MBAAEC",
                "file_size": 880,
                "file_path": "photos/file_9925.jpg",
                "width": 90,
                "height": 49
            },
            {
                "file_id": "AgADBQADHrExG-zz0VV6saCUHRVV3fZN1TIABFqsuVH5-nsE8KMBAAEC",
                "file_size": 17867,
                "width": 320,
                "height": 176
            },
            {
                "file_id": "AgADBQADHrExG-zz0VV6saCUHRVV3fZN1TIABIEipWgUGaHC8aMBAAEC",
                "file_size": 83111,
                "width": 800,
                "height": 440
            },
            {
                "file_id": "AgADBQADHrExG-zz0VV6saCUHRVV3fZN1TIABH5qw_SdDVtX7qMBAAEC",
                "file_size": 105548,
                "width": 954,
                "height": 525
            }
        ],
        "caption": "/debug",
        "caption_entities": [
            {
                "offset": 0,
                "length": 6,
                "type": "bot_command"
            }
        ]
    }
}';

$ch = curl_init("http://dev.bot.share/webhook/telegram.php");
curl_setopt_array($ch, 
    [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $a
    ]
);

print curl_exec($ch);
curl_close($ch);