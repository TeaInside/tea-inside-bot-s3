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
			if (preg_match("/^(\/|!|~)?sh\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "ShellExec@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~|admin)/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Admin@show");

		$this->set(function($d){
			if (preg_match("/\@admin/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Admin@call");

		$this->set(function($d){
			if (preg_match("/(\/|!|~)?t(r|l)\s([a-zA-Z]{2,5}|auto)\s([a-zA-Z]{2,5})(.*)$/Usi", $d["text"], $m)) {
				return [true, [trim($m[5]), trim($m[3]), trim($m[4])]];
			}
		}, "Translate@run");

		$this->set(function ($d){
			if (preg_match("/^((\/|!|~)?tlr)\s([a-zA-Z]{2,5}|auto)\s([a-zA-Z]{2,5})$/Usi", $d["text"], $m)) {
				return [true, [trim($m[3]), trim($m[4])]];
			}
		}, "Translate@tlr2");

		$this->set(function ($d){
			if (preg_match("/^((\/|!|~)?tlr)/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Translate@tlr");

		$this->set(function($d){
			$l = strtolower($d["text"]);

			if (substr($l, 0, 5) === "<?php") {
				return [true, ["php"]];
			}

			if (substr($l, 0, 5) === "<?py2") {
				return [true, ["python2"]];
			}

			if (substr($l, 0, 5) === "<?py3") {
				return [true, ["python3"]];
			}

			if (substr($l, 0, 4) === "<?py") {
				return [true, ["python"]];
			}

			if (substr($l, 0, 4) === "<?js") {
				return [true, ["js"]];
			}

			if (substr($l, 0, 6) === "<?java") {
				return [true, ["java"]];
			}

			if (substr($l, 0, 6) === "<?ruby") {
				return [true, ["ruby"]];
			}

			if (substr($l, 0, 5) === "<?c++") {
				return [true, ["c++"]];
			}

			if (substr($l, 0, 5) === "<?c") {
				return [true, ["c"]];
			}
		}, "VirtualizorLanguages@run");
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
