<?php

$ch = curl_init("https://webhook-a2.teainside.tech/webhook/github.php");
curl_setopt_array($ch, 
	[
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
	]
);
echo curl_exec($ch);
curl_close($ch);
