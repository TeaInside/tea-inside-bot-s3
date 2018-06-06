<?php

namespace Bot\Line;

use Bot\Telegram\Exe as TelegramExe;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line
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
			if ($this->data["chat_id"] === "Ce20228a1f1f98e6cf9d6f6338603e962") {
				return [true, []];
			}
		}, function () {


			$u = json_decode(
	            Exe::profile(
	                $this->data['user_id'], (
	                ($this->data['chat_type'] !== "private" ? $this->data['chat_id'] : null)
	                )
	            )['content'], true
	        );
	        isset($u['displayName']) or $u['displayName'] = $this->b['user_id'];
	        $msg = "<b>".htmlspecialchars($u['displayName'])."</b>\n".htmlspecialchars(str_replace("@Ammar F.", "@ammarfaizi2", $this->data["text"]));
			TelegramExe::bg()::sendMessage(
	         	[
					"text" => $msg,
					"chat_id" => "-1001128970273",
					"parse_mode" => "HTML"
	         	]
	        );
		});

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)?sh\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "ShellExec@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)admin/Usi", $d["text"])) {
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
