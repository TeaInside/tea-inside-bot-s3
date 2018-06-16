<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

use Isolator;
use Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Interpreter
 * @license MIT
 * @since 0.0.1
 */
class NodeJS extends Interpreter
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
		// $this->user = "www-data";
		// $this->version = "7.2";
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

		$st->setMemoryLimit(1024 * 1000);
		$st->setMaxProcesses(5);
		$st->setMaxWallTime(10);
		$st->setMaxExecutionTime(5);
		$st->setExtraTime(5);

		$st->run("/usr/bin/node /home/u".$id."/".$n);
		
		$rr = $st->getStdout();
		$rr.= $st->getStderr();

		return $rr;
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return substr(sha1(sha1($this->code).md5($this->code)), 0, 5).".js";
	}
}
