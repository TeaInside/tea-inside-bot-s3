<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
trait ResponseRoutes
{
	/**
	 * @var array
	 */
	private $routes = [];


	/**
	 * @return void
	 */
	public function buildRoutes()
	{
		$this->set(function($d){
			if (preg_match("/^(\/sh|!sh|sh)\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "ShellExec@run");
	}

	/**
	 * @param callable        $condition
	 * @param callable|string $action
	 * @return void
	 */
	public function set(callable $condition, $action)
	{
		$this->routes[] = [$condition, $action];
	}
}
