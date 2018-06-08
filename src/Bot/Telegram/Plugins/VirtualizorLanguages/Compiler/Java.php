<?php

namespace Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

use Bot\Telegram\Plugins\VirtualizorLanguages\Compiler;

defined("VIRTUALIZOR_STORAGE_JAVA") or die("VIRTUALIZOR_STORAGE_JAVA is not defined!");
defined("VIRTUALIZOR_BINARY_JAVA") or die("VIRTUALIZOR_BINARY_JAVA is not defined!");

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
	public $version;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var string
	 */
	protected $binName;

	/**
	 * @var string
	 */
	private $compile;

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
	protected function compile()
	{
		$this->filename = $filename = VIRTUALIZOR_STORAGE_JAVA."/code/".($this->binName = $this->generateFilename()).".java";
		is_dir(VIRTUALIZOR_STORAGE_JAVA) or mkdir(VIRTUALIZOR_STORAGE_JAVA);
		is_dir(VIRTUALIZOR_STORAGE_JAVA."/bin") or mkdir(VIRTUALIZOR_STORAGE_JAVA."/bin");
		is_dir(VIRTUALIZOR_STORAGE_JAVA."/code") or mkdir(VIRTUALIZOR_STORAGE_JAVA."/code");
		$handle = fopen($filename, "w");
		fwrite($handle, $this->code);
		fflush($handle);
		fclose($handle);
		$this->compile = shell_exec(("sudo ".VIRTUALIZOR_BINARY_JAVA[$this->version][0])." ".$filename." -d ".VIRTUALIZOR_STORAGE_JAVA."/bin 2>&1 && echo compiled_successfully");
		return (bool) preg_match("/compiled_successfully/", $this->compile);
	}

	/**
	 * @return string
	 */
	public function run()
	{
		if ($this->compile()) {
			return str_replace(
				realpath(VIRTUALIZOR_STORAGE_JAVA), 
				"/tmp", 
				shell_exec("cd ".VIRTUALIZOR_STORAGE_JAVA."/bin && sudo -u ".VIRTUALIZOR_BINARY_JAVA[$this->version][1]." ".$this->binName." 2>&1")
			);
		} else {
			return str_replace(VIRTUALIZOR_STORAGE_JAVA, "/tmp", $this->compile);
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
