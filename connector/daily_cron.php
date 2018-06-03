<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../config/telegram/main.php";

$now = date("Y-m-d H:i:s");
$aaa = microtime(true);

if (file_exists($f = logs."/daily_cron/.daily_count")) {
	$count = ((int) file_get_contents($f)) + 1;
} else {
	$count = 1;
}

$sh = shell_exec("rm -rfv ".VIRTUALIZOR_STORAGE_PHP."/*")."\n";
$sh.= shell_exec("rm -rfv ".VIRTUALIZOR_STORAGE_PYTHON."/*")."\n";
$sh.= shell_exec("rm -rfv ".VIRTUALIZOR_STORAGE_NODEJS."/*")."\n";
$sh.= shell_exec("rm -rfv /tmp/*")."\n";

$end = date("Y-m-d H:i:s");
$aaa = microtime(true)-$aaa;

file_put_contents($f, $count);
file_put_contents($f = logs."/daily_cron/".$count.".log", "Start: $now\n\n".$sh."\n\nEnd: $end\nElapsed time: ".$aaa);

foreach(SUDOERS as $sudoer) {
	Exe::sendMessage(
		[
			"text" => "<b>Daily Cron Report</b>\nStart: $now\nEnd: $end\nElapsed time: {$aaa} s\n\nSaved in $f",
			"chat_id" => $sudoer,
			"parse_mode" => "HTML"
		]
	);
}
