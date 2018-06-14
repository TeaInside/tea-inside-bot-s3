<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

use Isolator;
use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Bot\Telegram\Plugins\VirtualizorLanguages\Compiler
 * @license MIT
 * @since 0.0.1
 */
class C extends Compiler
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
	protected $filename;

	/**
	 * @var string
	 */
	protected $binName;

	/**
	 * @var \Isolator
	 */
	private $Isolator;

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
		$this->version = "*";
	}

	/**
	 * @return bool
	 */
	protected function compile($userId)
	{
		$id = Isolator::generateUserId($userId);
		$this->isolator = new Isolator($id);
		if (! file_exists($f = ISOLATOR_HOME."/".$id."/u".$id."/".($n = $this->generateFilename()).".c")) {
			file_put_contents($f, $this->code);
			$exe = trim(shell_exec("sudo gcc ".$f." -o ".ISOLATOR_HOME."/".$id."/u".$id."/".$n." && echo success"));
			return preg_match("/success/i", $exe) ? $n : false;
		} else {
			return $n;
		}
	}

	/**
	 * @return string
	 */
	public function run($userId)
	{
		if ($bin = $this->compile($userId)) {

			$st->setMemoryLimit(1024 * 256);
			$st->setMaxProcesses(5);
			$st->setMaxWallTime(30);
			$st->setMaxExecutionTime(15);
			$st->setExtraTime(5);

			$st->run("/home/u".$id."/".$n);
			
			$rr = $st->getStdout();
			$rr.= $st->getStderr();

			return $rr;

		} else {
			return "Error!";
		}
	}

	/**
	 * @return string
	 */
	private function generateFilename()
	{
		return sha1($this->code);
	}
}
