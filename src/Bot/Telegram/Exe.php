<?php

namespace Bot\Telegram;

use Bot\Telegram\Exceptions\InvalidJsonDataException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
final class Exe
{
	/**
	 * @return \Bot\Telegram\BackgrounProcess
	 */
	public static function bg()
	{
		return new BackgroundProcess;
	}

	/**
	 * @param string $method
	 * @param array  $parameters
	 * @return array
	 */
	public function __call($method, $parameters)
	{
		return self::{$method}(...$parameters);
	}

	/**
	 * @param string $method
	 * @param array  $parameters
	 * @return array
	 */
	public static function __callStatic($method, $parameters)
	{
		return self::exec(
			$method, 
			(isset($parameters[0]) ? $parameters[0] : null),
			(isset($parameters[1]) ? $parameters[1] : "POST")
		);
	}

	/**
	 * @param string      $method
	 * @param array|null  $postData
	 * @param string	  $method
	 */
	private static function exec(string $method, $postData, $httpMethod)
	{
		if ($httpMethod === "GET") {
			$ch = curl_init(
				"https://api.telegram.org/bot".TOKEN."/".$method."?".http_build_query($postData)
			);
			curl_setopt_array($ch, 
				[
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_SSL_VERIFYPEER => false
				]
			);
		} else {
			$ch = curl_init(
				"https://api.telegram.org/bot".TOKEN."/".$method
			);
			curl_setopt_array($ch, 
				[
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query($postData),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_SSL_VERIFYPEER => false
				]
			);
		}

		$out = curl_exec($ch);
		$info = curl_getinfo($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);

		return [
			"out" => $out,
			"info" => $info,
			"errno" => $errno,
			"error" => $error
		];
	}
}
