<?php

$f = fopen("data.txt", "w+");

$ip = $_SERVER["REMOTE_ADDR"];

$data = array_merge($_POST, [
	"ip"	=> $ip
]);

fwrite($f, json_encode($data));
fclose($f);