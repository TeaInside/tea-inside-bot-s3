<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
final class BackgroundProcess
{
	/**
	 * @param string $method
	 * @param array  $parameters
	 * @return void
	 */
	public function __call($method, $parameters)
	{
		shell_exec(
			"nohup "
			.PHP_BINARY." "
			.__DIR__."/../../../connector/telegram_bridge.php \"{$method}\" \""
			.rawurlencode(json_encode($parameters))."\" >> "
			.logs."/telegram/bgpc.log 2>&1 &"
		);
	}

	/**
	 * @param string $method
	 * @param array  $parameters
	 * @return void
	 */
	public static function __callStatic($method, $parameters)
	{
		shell_exec(
			"nohup "
			.PHP_BINARY." "
			.__DIR__."/../../../connector/telegram_bridge.php \"{$method}\" \""
			.rawurlencode(json_encode($parameters))."\" >> "
			.logs."/telegram/bgpc.log 2>&1 &"
		);
	}
}
