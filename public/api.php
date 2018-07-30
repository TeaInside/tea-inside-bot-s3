<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config/telegram/main.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (isset($_GET["method"])) {
	switch (strtolower($_GET["method"])) {
		case 'getchat':
			goto getChat;
			break;
		
		default:
			goto methodNotFound;
			break;
	}
} else {
	err("Parameter required: \"method\"");
}


getChat:

	if (! isset($_GET["group_id"])) {
		err("Parameter required: \"group_id\"");
	}

	if (isset($_GET["limit"])) {
		if (! is_numeric($_GET["limit"])) {
			err("Invalid Parameter: \"limit\" must be an integer");
		}
	} else {
		$_GET["limit"] = 25;
	}

	if (isset($_GET["offset"])) {
		if (! is_numeric($_GET["offset"])) {
			err("Invalid Parameter: \"offset\" must be an integer");
		}
	} else {
		$_GET["offset"] = 0;
	}

	$st = new Bot\Telegram\Api\GetChatApi;
	if ($st->groupExists($_GET["group_id"])) {
		$st = $st->getNewChat($_GET["group_id"], $_GET["limit"], $_GET["offset"]);
		foreach($st as &$q) {
			$q["first_name"] = htmlspecialchars($q["first_name"]);
			$q["last_name"] = is_null($q["last_name"])? NULL : htmlspecialchars($q["last_name"]);  $q["text"] = htmlspecialchars($q["text"]);
		}
		ssk($st);
	} else {
		err("Group \"{$_GET['group_id']}\" does not exist");
	}

exit(0);




methodNotFound:

	err("Method \"{$_GET['method']}\" does not exist");

exit(0);













function err($errorMsg)
{
	http_response_code(400);
	print json_encode(
		[
			"code" => 400,
			"status" => "error",
			"message" => $errorMsg
		]
	);
	exit;
}

function ssk($msg)
{
	http_response_code(200);
	print json_encode(
		[
			"code" => 200,
			"status" => "OK",
			"message" => $msg
		]	
	);
	exit;
}
