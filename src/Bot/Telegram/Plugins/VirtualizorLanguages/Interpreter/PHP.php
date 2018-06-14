<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

use Isolator;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

defined("VIRTUALIZOR_STORAGE_PHP") or die("VIRTUALIZOR_STORAGE_PHP is not defined!");
defined("VIRTUALIZOR_BINARY_PHP") or die("VIRTUALIZOR_BINARY_PHP is not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter
 * @license MIT
 * @since 0.0.1
 */
class PHP extends Interpreter
{
	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	public $user;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @param string $code
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct($code)
	{
		$this->code = $code;
		$this->user = "www-data";
		$this->version = "7.2";
	}

	/**
	 * @return string
	 */
	public function run($userId)
	{
		$st = new Isolator(Isolator::generateUserId($userId));
		$st->setMemoryLimit(1024 * 256);
		$st->setMaxProcesses(5);
		$st->setMaxWallTime(30);
		$st->setMaxExecutionTime(15);
		$st->setExtraTime(5);
		$st->run("/usr/bin/php7.2 ".$filename);
		$rr = "";
		$rr = $st->getStdout();
		$rr.= $st->getStderr();
		return str_replace(realpath(VIRTUALIZOR_STORAGE_PHP), "/tmp", $st);
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		if ($this->user === "limited") {
			$a = explode("<?php", $this->code, 2);
			$a[0] ="<?php set_time_limit(3); ini_set(\"max_execution_time\", 3); ini_set(\"memory_limit\", \"5M\"); ";
			$this->code = $a[0]." ".$a[1];
		}
		return substr(sha1(sha1($this->code).md5($this->code)), 0, 5).".php";
	}
}
