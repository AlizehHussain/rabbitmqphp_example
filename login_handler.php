<?php
// read the input the login page sends
$data = json_decode(file_get_contents("php://input"), true);
// sends back a reply saying the login was sucessful and a token for it
//placeholder untill rabbitmq and database are connected
echo json_encode(["success" => true, "token" => "abc123"]);
?>