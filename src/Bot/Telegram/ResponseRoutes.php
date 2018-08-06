<?php

namespace Bot\Telegram;

use Bot\Line\Exe as LineEXE;

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
			return [$this->data["voice"]["file_id"] === "AwADBQADVQADp1TpVnBDarzQ8GkPAg", []];
		}, function () {
			Exe::deleteMessage(
				[
					"message_id" => $this->data["msg_id"],
					"chat_id" => $this->data["chat_id"]
				]
			);

			Exe::sendMessage(
				[
					"chat_id" => $this->data["chat_id"],
					"text" => "Sorry ".$this->data["first_name"].", the type of media you have sent is not allowed in this chat. Due to the security reason we deleted your message."
				]
			);
			echo 123;
		});

		$this->set(function($d){
			if ($this->data["chat_id"]."" === "-1001134449138") {
				return [true, ["Ce20228a1f1f98e6cf9d6f6338603e962"]];
			}
		}, "Solid@run");

		if (preg_match("/\@".bot_username."/i", $this->data["text"])) {
			$this->set(function($d){
				if (preg_match("/(to?pi?k)\s{1,4}kulgram\s{1,4}(ha?ri|ka?li)\s{1,4}ini\s{1,4}(.*)$/Usi", $d["text"], $m)) {
					return [true, [$m[3]]];
				} elseif (preg_match("/ju?du?l\s{1,4}kulgram\s{1,4}(ha?ri|ka?li)\s{1,4}ini\s{1,4}(.*)$/Usi", $d["text"], $m)) {
					return [true, [$m[2]]];
				}
			}, "Kulgram@init");

			$this->set(function($d){
				if (preg_match("/mu?lai\s{1,4}(nyatet|mencatat|nyatat)/Usi", $d["text"], $m)) {
					return [true, []];
				}
			}, "Kulgram@start");

			$this->set(function($d){
				if (preg_match("/he?ntika?n\s{1,4}(ca?ta?ta?n|nyatet|mencatat|nyatat)/Usi", $d["text"], $m)) {
					return [true, []];
				}
			}, "Kulgram@stop");
		}

		$this->set(function($d){
			if ($this->data["msg_type"] === "new_chat_members") {
				return [true, []];
			}
		}, "Welcome@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)welcome\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "Admin@setWelcome");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)?sh\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "ShellExec@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~|\#)?(ask2|stackoverflow)\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[3]]];
			}
		}, "Ask@stackoverflow");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~|\#)?ask\s(.*)$/Usi", $d["text"], $m)) {
				return [true, [$m[2]]];
			}
		}, "Ask@brainly");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)debug(.+)?/Usi", $d["text"], $m)) {
				return [true, []];
			}
		}, "Debug@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)admin(\s.*)?/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Admin@show");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)promote(\s.*)?/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Admin@promote");

		$this->set(function($d){
			if (preg_match("/\@admin/Usi", $d["text"])) {
				return [true, []];
			}
		}, "Admin@call");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)ban(\s.+)?$/", $d["text"], $m)) {
				isset($m[2]) or $m[2] = "";
				return [true, [$m[2]]];
			}
		}, "Admin@ban");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)kick(\s.+)?$/", $d["text"], $m)) {
				isset($m[2]) or $m[2] = "";
				return [true, [$m[2]]];
			}
		}, "Admin@kick");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)?t(r|l)\s([a-zA-Z]{2,5}|auto)\s([a-zA-Z]{2,5})(.*)$/Usi", $d["text"], $m)) {
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
				$this->data["__code"] = $this->data["text"];
				return [true, ["php"]];
			}

			if (substr($l, 0, 5) === "<?py2") {
				$this->data["__code"] = substr($this->data["text"], 5);
				return [true, ["python2"]];
			}

			if (substr($l, 0, 5) === "<?py3") {
				$this->data["__code"] = substr($this->data["text"], 5);
				return [true, ["python3"]];
			}

			if (substr($l, 0, 4) === "<?py") {
				$this->data["__code"] = substr($this->data["text"], 4);
				return [true, ["python"]];
			}

			if (substr($l, 0, 4) === "<?js") {
				$this->data["__code"] = substr($this->data["text"], 4);
				return [true, ["js"]];
			}

			if (substr($l, 0, 8) === "<?nodejs") {
				$this->data["__code"] = substr($this->data["text"], 8);
				return [true, ["js"]];
			}

			if (substr($l, 0, 6) === "<?node") {
				$this->data["__code"] = substr($this->data["text"], 6);
				return [true, ["js"]];
			}

			if (substr($l, 0, 4) === "<?pl") {
				$this->data["__code"] = substr($this->data["text"], 4);
				return [true, ["perl"]];
			}

			if (substr($l, 0, 4) === "<?rb") {
				$this->data["__code"] = substr($this->data["text"], 4);
				return [true, ["ruby"]];
			}

			if (substr($l, 0, 6) === "<?perl") {
				$this->data["__code"] = substr($this->data["text"], 6);
				return [true, ["perl"]];
			}

			if (substr($l, 0, 6) === "<?java") {
				$this->data["__code"] = substr($this->data["text"], 6);
				return [true, ["java"]];
			}

			if (substr($l, 0, 6) === "<?ruby") {
				$this->data["__code"] = substr($this->data["text"], 6);
				return [true, ["ruby"]];
			}

			if (substr($l, 0, 5) === "<?cpp") {
				$this->data["__code"] = substr($this->data["text"], 5);
				return [true, ["c++"]];
			}

			if (substr($l, 0, 5) === "<?c++") {
				$this->data["__code"] = substr($this->data["text"], 5);
				return [true, ["c++"]];
			}

			if (substr($l, 0, 3) === "<?c") {
				$this->data["__code"] = substr($this->data["text"], 3);
				return [true, ["c"]];
			}

			if (substr($l, 0, 5) === "<?asm") {
				$this->data["__code"] = substr($this->data["text"], 5);
				return [true, ["asm"]];
			}
		}, "VirtualizorLanguages@run");

		$this->set(function($d){
			if (preg_match("/^(\/|!|~)me/", $d["text"], $m)) {
				return [true, []];
			}
		}, "Me@me");
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
