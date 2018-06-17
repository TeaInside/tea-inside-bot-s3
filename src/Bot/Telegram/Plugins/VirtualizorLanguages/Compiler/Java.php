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
class Java extends Compiler
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

		$javaDir = ISOLATOR_HOME."/".$id."/u".$id."/java";
		is_dir($javaDir) or mkdir($javaDir);
		is_dir($javaDir."/class") or mkdir($javaDir."/class");

		file_put_contents($f = $javaDir."/".($n = $this->generateFilename()).".java", $this->code);
		$exe = trim(shell_exec("sudo javac ".$f." -d ".$javaDir."/class && echo success"));
		return preg_match("/success/i", $exe) ? $n : false;
	}

	/**
	 * @return string
	 */
	public function run($userId)
	{
		if ($bin = $this->compile($userId)) {

			$this->isolator->setMemoryLimit(1024 * 256 * 10);
			$this->isolator->setMaxProcesses(15);
			$this->isolator->setMaxWallTime(10);
			$this->isolator->setMaxExecutionTime(5);
			$this->isolator->setExtraTime(3);

			$w = $this->isolator->run("/bin/sh -c \"cd /home/u".$this->uniqueId."/java/class; /etc/alternatives/java ".$bin." 2>&1\"");
			
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
		$a = explode("class", $this->code, 2);
		$a = preg_replace("/[^\w\d\_]/", "~", trim($a[1]));
		$a = explode("~", $a, 2);
		return $a[0];
	}
}
