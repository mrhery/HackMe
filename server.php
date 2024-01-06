<?php

$f = fopen("data.txt", "w+");
fwrite($f, json_encode($_POST));
fclose($f);