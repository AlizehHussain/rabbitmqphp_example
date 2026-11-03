<?php

header("Content-Type: application/json");
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once ('rabbitMQLib.inc');

// read the JSON body the browser sent
$data = json_decode(file_get_contents("php://input"), true);

$venue  = trim($data['venue'] ?? "");
$city   = trim($data['city'] ?? "");
$token  = $data['sessionToken'] ?? "";
$review = trim($data['review'] ?? "");

// need a venue and some text
if ($venue === "" || $review === "") {
	echo json_encode([
		"success" => false,
		"message" => "Venue and review required"
	]);
	exit;
}

try {
	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

	// key names must match what the "save_venue_review" case reads in the listener
	$request = [
		"type"    => "save_venue_review",
		"token"   => $token,
		"comment" => $review,
		"venue"   => $venue,
		"city"    => $city
	];

	$response = $client->send_request($request);

	if (is_array($response) && ($response["success"] ?? false) === true) {
		echo json_encode(["success" => true]);
	} else {
		echo json_encode([
			"success" => false,
			"message" => (is_array($response) ? ($response["message"] ?? null) : null) ?? "Could not save review"
		]);
	}
} catch (Throwable $e) {
	error_log("Venue review handler error: " . $e->getMessage());
	echo json_encode(["success" => false, "message" => "Unable to reach backend"]);
}

?>
