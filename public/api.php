<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config/telegram/main.php";

header("Content-Type: application/json");

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
	}

	$st = new Bot\Telegram\Api\GetChatApi;
	if ($st->groupExists()) {
		ssk($st->getNewChat($_GET["group_id"], $_GET["limit"]));	
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
