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
		if (! file_exists($f = ISOLATOR_HOME."/".$id."/u".$id."/".($n = $this->generateFilename()).".asm")) {
			file_put_contents($f, $this->code);
			$exe = trim(shell_exec("sudo nasm -f elf ".$f." -o ".ISOLATOR_HOME."/".$id."/u".$id."/".$n.".o 2>&1"));
			var_dump("exe1: ".$exe);
			if (empty($exe)) {
				$exe = trim(shell_exec(
					"sudo ld -m elf_i386 -s -o ".ISOLATOR_HOME."/".$id."/u".$id."/".$n." ".ISOLATOR_HOME."/".$id."/u".$id."/".$n.".o 2>&1"
				));
				var_dump("exe2: ".$exe);
			}
			return empty($exe) ? $n : false;
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
			$this->isolator->setMaxProcesses(3);
			$this->isolator->setMaxWallTime(10);
			$this->isolator->setMaxExecutionTime(5);
			$this->isolator->setExtraTime(3);

			$w = $this->isolator->run("/home/u".$this->uniqueId."/".$bin." 2>&1");
			
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
