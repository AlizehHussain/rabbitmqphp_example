<?php
//generates a session key 
function keyGenerator($userID){
	$key = bin2hex(random_bytes(32));

	return $key;
}

?>
