<?php
chdir(__DIR__ . '/..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$token = $_COOKIE['sessionToken'] ?? null;
if (!$token) { header('Location: /login.php'); exit; }

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$resp = $client->send_request(['type' => 'validate_session', 'session_token' => $token]);
if (empty($resp['success'])) { header('Location: /login.php'); exit; }

$name = $resp['username'] ?? ('User #' . $resp['user_id']);
?>
<h1>Welcome, <?= htmlspecialchars($name) ?></h1>
<a href="logout.php">Log out</a>
