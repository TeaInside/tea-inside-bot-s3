<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

use Isolator;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

defined("VIRTUALIZOR_STORAGE_PYTHON") or die("VIRTUALIZOR_STORAGE_PYTHON is not defined!");
defined("VIRTUALIZOR_BINARY_PYTHON") or die("VIRTUALIZOR_BINARY_PYTHON is not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter
 * @license MIT
 * @since 0.0.1
 */
class Python extends Interpreter
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
		$this->version = "3";
	}

	/**
	 * @param int $userId
	 * @return string
	 */
	public function run($userId)
	{
		$id = Isolator::generateUserId($userId);
		$st = new Isolator($id);

		if (! file_exists($f = ISOLATOR_HOME."/".$id."/u".$id."/".($n = $this->generateFilename()))) {
			file_put_contents($f, $this->code);
		}

		$st->setMemoryLimit(1024 * 256);
		$st->setMaxProcesses(5);
		$st->setMaxWallTime(30);
		$st->setMaxExecutionTime(15);
		$st->setExtraTime(5);

		if ($this->version == 3) {
			$bin = "/usr/bin/python3.5";
		} else {
			$bin = "/usr/bin/python2.7";
		}

		$st->run($bin." /home/u".$id."/".$n);
		
		$rr = $st->getStdout();
		$rr.= $st->getStderr();

		return str_replace(realpath(VIRTUALIZOR_STORAGE_PHP), "/tmp", $rr);
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return substr(sha1(sha1($this->code).md5($this->code)), 0, 5).".py";
	}
}
