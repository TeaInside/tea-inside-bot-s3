<?php

use Exceptions\IsolatorException;
use Contracts\Isolator as IsolatorContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 * @since 0.0.1
 */
final class Isolator implements IsolatorContract
{
	/**
	 * @var string
	 */
	private $cmd;

	/**
	 * @var int
	 */
	private $boxId = 0;

	/**
	 * @var string
	 */
	private $user = "root";

	/**
	 * @var string
	 */
	private $boxDir = "/var/local/lib/isolate/0/box";

	/**
	 * @var int
	 */
	private $maxWallTime;

	/**
	 * @var int
	 */
	private $extraTime;

	/**
	 * @var int
	 */
	private $maxProcesses = 1;

	/**
	 * @var int
	 */
	private $maxExecutionTime = 30;

	/**
	 * @var int
	 */
	private $memoryLimit = 102400;

	/**
	 * @var bool
	 */
	private $isExecuted = false;

	/**
	 * @param string $cmd
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(string $cmd)
	{
		$this->cmd = $cmd;
	}
 
	/**
	 * @param string $user
	 * @return void
	 *
	 * Set user.
	 */
	public function setUser(string $user)
	{
		$this->user = $user;
	}

	/**
	 * @param int $max
	 * @return void
	 * 
	 * Limit address space of the program to size kilobytes. 
	 * If more processes are allowed, this applies to each of them separately. 
	 */
	public function setMemoryLimit(int $max)
	{
		$this->memoryLimit = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Limit run time of the program to time seconds. Fractional numbers are allowed. 
	 * Time in which the OS assigns the processor to different tasks is not counted. 
	 */
	public function setMaxExecutionTime(int $max)
	{
		$this->maxExecutionTime = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Limit wall-clock time to time seconds. 
	 * Fractional values are allowed. 
	 * This clock measures the time from the start of the program to its exit, 
	 * so it does not stop when the program has lost the CPU or when it is waiting for an external event. 
	 * We recommend to use --time as the main limit, 
	 * but set --wall-time to a much higher value as a precaution against sleeping programs. 
	 */
	public function setMaxWallTime(int $max)
	{
		$this->maxWallTime = $max;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * When a time limit is exceeded,
	 * wait for extra time seconds before killing the program.
	 * This has the advantage that the real execution time is reported,
	 * even though it slightly exceeds the limit. Fractional numbers are again allowed.
	 */
	public function setExtraTime(int $max)
	{
		$this->extraTime = $max;
	}

	/**
	 * @param int $boxId
	 * @return void
	 *
	 * When you run multiple sandboxes in parallel, 
	 * you have to assign each unique IDs to them by this option. 
	 * See the discussion on UIDs in the INSTALLATION section. 
	 * The ID defaults to 0. 
	 */
	public function setBoxId(int $boxId)
	{
		$this->boxId = $boxId;
	}

	/**
	 * @param int $max
	 * @return void
	 *
	 * Permit the program to create up to max processes and/or threads. 
	 * Please keep in mind that time and memory limit do not work with multiple 
	 * processes unless you enable the control group mode. 
	 * If max is not given, an arbitrary number of processes can be run. 
	 * By default, only one process is permitted. 
	 */
	public function setMaxProcesses(int $max)
	{
		$this->maxProcesses = $max;
	}

	/**
	 * @return string
	 */
	public function getCmd(): string
	{
		return $this->cmd;
	}

	/**
	 * @return void
	 */
	public function run()
	{
		shell_exec(
			$this->cmd = 
			"sudo -u ".$this->user." ".
			"/usr/local/bin/isolate ".
			$this->param("memoryLimit").
			$this->param("maxExecutionTime").
			$this->param("maxWallTime").
			$this->param("extraTime").
			$this->param("boxId").
			$this->param("maxProcesses").
			$this->param("stdout").
			$this->param("stderr").
			"--run -- ".$this->cmd.
			" 2>&1"
		);
		$this->isExecuted = true;
	}

	/**
	 * @param string $r
	 * @return string
	 */
	private function param(string $r): string
	{
		$param = "";
		switch ($r) {
			case 'memoryLimit':
				$param = isset($this->memoryLimit) ? "--mem=".$this->memoryLimit : "";
				break;
			case "maxExecutionTime":
				$param = isset($this->maxExecutionTime) ? "--time=".$this->maxExecutionTime : "";
				break;
			case "maxWallTime":
				$param = isset($this->maxWallTime) ? "--wall-time=".$this->maxWallTime : "";
				break;
			case "extraTime":
				$param = isset($this->extraTime) ? "--extra-time=".$this->extraTime : "";
				break;
			case "boxId":
				if (isset($this->boxId)) {

					if (! is_dir(
						$this->boxDir = $d = "/var/local/lib/isolate/".$this->boxId."/box"
					)) {
						shell_exec("sudo mkdir -p ".$d);
					}

					if (! is_dir($d = "/var/local/lib/isolate/".$this->boxId."/root")) {
						shell_exec("sudo mkdir -p ".$d);
					}

					$param = "--box-id=".$this->boxId;
				}
				break;
			case "stdout":
				$param = "--stdout=stdout";
				break;
			case "stderr":
				$param = "--stderr=stderr";
				break;
			case "maxProcesses":
				$param = isset($this->maxProcesses) ? "--processes=".$this->maxProcesses : "";
				break;
			default:
				break;
		}

		return $param === "" ? "" : $param." ";
	}

	/**
	 * @return string
	 */
	public function getStdout(): string
	{
		if (! $this->isExecuted) {
			throw new IsolatorException("The command is not executed yet!");
		}

		if (! file_exists($this->boxDir."/stdout")) {
			throw new IsolatorException("stdout file does not exists!");
		}

		return file_get_contents($this->boxDir."/stdout");
	}

	/**
	 * @return string
	 */
	public function getStderr(): string
	{
		if (! $this->isExecuted) {
			throw new IsolatorException("The command is not executed yet!");
		}

		if (! file_exists($this->boxDir."/stdout")) {
			throw new IsolatorException("stderr file does not exists!");
		}

		return file_get_contents($this->boxDir."/stderr");
	}
}
