<?php
chdir('/var/www/rabbitmq');
require_once('/var/www/rabbitmq/path.inc');
require_once('/var/www/rabbitmq/get_host_info.inc');
require_once('/var/www/rabbitmq/rabbitMQLib.inc');

$token = $_COOKIE['sessionToken'] ?? null;
if ($token) {
    $client = new rabbitMQClient("/var/www/rabbitmq/testRabbitMQ.ini", "testServer");
    $client->send_request(['type' => 'logout', 'sessionToken' => $token]);
}
setcookie('sessionToken', '', time() - 3600, '/');
header('Location: login.php');
exit;
