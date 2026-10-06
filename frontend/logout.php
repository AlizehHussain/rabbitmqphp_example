<?php
chdir(__DIR__ . '/..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$token = $_COOKIE['sessionToken'] ?? null;
if ($token) {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $client->send_request(['type' => 'logout', 'sessionToken' => $token]);
}
setcookie('sessionToken', '', time() - 3600, '/');
header('Location: /login.php');
exit;
