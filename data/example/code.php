<?php
$stdin = fopen("php://stdin","r");
while($i = trim(fgets($stdin))){
	echo ($i*$i)."\n";
	}
fclose($stdin);
?>