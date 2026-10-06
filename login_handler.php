<?php

include 'sessionKey.php'; //required to create session key 
// read the input the login page sends
$data = json_decode(file_get_contents("php://input"), true);
// sends back a reply saying the login was sucessful and a token for it
//placeholder untill rabbitmq and database are connected
$userid = 1; // default userID till database contains actually userIDs
echo json_encode(["success" => true, "token" => keyGenerator($userid)]); //keyGenerator is a function from sessionKey.php
?>
