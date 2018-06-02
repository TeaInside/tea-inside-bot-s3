<?php

require __DIR__."/../../config/telegram/log_dir";

$in = file_get_contents("php://input");

shell_exec(
	"nohup ".
	"/usr/bin/php".
	" ".
	__DIR__."/../../connector/telegram.php \"".
	rawurlencode($in)."\" >> \"{$logDir}/bg.log\" 2>&1 &"
);
