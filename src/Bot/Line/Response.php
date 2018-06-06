<?php

namespace Bot\Line;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line
 * @license MIT
 * @since 0.0.1
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var \Bot\Line\Data
	 */
	private $data;

	/**
	 * @param \Bot\Line\Data $data
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
		if (in_array($this->data["msg_type"], ["text", "image"])) {
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
					$route[1][0] = "\\Bot\\Line\\Responses\\".$route[1][0];
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
		// $logger = new User($this->data);
		// $logger->run();
		// if ($this->data["chat_type"] !== "private") {
		// 	$logger = new Group($this->data);
		// 	$logger->run();
		// }
		// $logger = new Message($this->data);
		// $logger->run();
	}
}
