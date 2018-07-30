<?php

use Bot\Telegram\Data;
use Bot\Telegram\Logger\User;
use Bot\Telegram\Logger\Group;
use Bot\Telegram\Logger\Message;

if (isset($argv[1])) {
	require __DIR__."/../vendor/autoload.php";
	require __DIR__."/../config/telegram/main.php";
	try {
		$logger = new User($data = new Data(rawurldecode($argv[1])));
		$logger->run();
		if ($data["chat_type"] !== "private") {
			$logger = new Group($data);
			$logger->run();
		}
		$logger = new Message($data);
		$logger->run();
	} catch (Error $e) {
		var_dump($e->getMessage(), "\n\n".rawurldecode($argv[1])."\n\n");
	}
}
