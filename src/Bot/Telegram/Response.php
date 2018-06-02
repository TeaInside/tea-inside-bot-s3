<?php

namespace Bot\Telegram;

use DB;
use Bot\Telegram\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Telegram
 * @license MIT
 * @since 0.0.1
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

	/**
	 * @param \Bot\Telegram\Data $data
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}

	/**
	 * @return void
	 */
	public function run()
	{
		if ($this->data["msg_type"] === "text") {
			$this->buildRoutes();
			$this->action();
		}
		$this->saveResponse();
	}

	/**
	 * @return void
	 */
	private function action()
	{
		foreach($this->routes as $route) {
			$e = $route[0]($this->data);
			if (is_array($e) && $e[0]) {
				if (is_string($route[1])) {
					$route[1] = explode("@", $route[1], 2);
					$route[1][0] = "\\Bot\\Telegram\\Responses\\".$route[1][0];
					$st = new $route[1][0]($this->data);
					if (call_user_func_array([$st, $route[1][1]], $e[1])) {
						break;
					}
				} elseif (is_callable($route[1])) {
					if ($route[1]()) {
						break;
					}
				}
			}
		}
	}

	/**
	 * @return void
	 */
	private function saveResponse()
	{

	}
}
