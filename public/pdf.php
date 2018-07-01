<?php
ini_set("display_errors", true);
require __DIR__."/../vendor/autoload.php";

$mpdf = new \Mpdf\Mpdf(
	['tempDir' => '/tmp']
);
$mpdf->WriteHTML('<h1>Hello world!</h1>');
ob_start();
$mpdf->Output();
$f = ob_get_clean();
header("Content-Type: text/plain");
file_put_contents("test.pdf", $f);