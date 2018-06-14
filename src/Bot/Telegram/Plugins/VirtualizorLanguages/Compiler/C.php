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
		$id = $this->uniqueId = Isolator::generateUserId($userId);
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

			$this->isolator->setMemoryLimit(1024 * 256);
			$this->isolator->setMaxProcesses(5);
			$this->isolator->setMaxWallTime(30);
			$this->isolator->setMaxExecutionTime(15);
			$this->isolator->setExtraTime(5);

			$w = $this->isolator->run("/home/u".$this->uniqueId."/".$bin);
			
			$rr = $this->isolator->getStdout();
			$rr.= $this->isolator->getStderr();
			$rr.= "\n\n".$w;

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
