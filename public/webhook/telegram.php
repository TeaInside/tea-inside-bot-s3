<?php

require __DIR__."/../../config/telegram/log_dir";

$in = file_get_contents("php://input");

shell_exec(
	"nohup ".
	PHP_BINARY.
	" ".
	__DIR__."/../../connector/telegram.php \"".
	rawurlencode($in)."\" >> \"{$logDir}\" 2>&1 &"
);
