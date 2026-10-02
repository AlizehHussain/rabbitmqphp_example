<?php
chdir('/var/www/rabbitmq');
require_once('/var/www/rabbitmq/path.inc');
require_once('/var/www/rabbitmq/get_host_info.inc');
require_once('/var/www/rabbitmq/rabbitMQLib.inc');

$token = $_COOKIE['sessionToken'] ?? null;
if (!$token) { header('Location: login.php'); exit; }

$client = new rabbitMQClient("/var/www/rabbitmq/testRabbitMQ.ini", "testServer");
$resp = $client->send_request(['type' => 'validate_session', 'sessionToken' => $token]);
if (empty($resp['valid'])) { header('Location: login.php'); exit; }
?>
<h1>Welcome, <?= htmlspecialchars($resp['username']) ?></h1>
<a href="logout.php">Log out</a>
