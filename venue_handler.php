<?php

header("Content-Type: application/json");
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once ('rabbitMQLib.inc');

// read the JSON body the browser sent
$data = json_decode(file_get_contents("php://input"), true);

$venue   = trim($data['venue'] ?? "");
$city    = trim($data['city'] ?? "");
$token   = $data['sessionToken'] ?? "";
$clicked = ($data['clicked'] ?? false) === true;
$rating  = (int) ($data['rating'] ?? 0);

// a venue name is required
if ($venue === "") {
	echo json_encode(["success" => false, "message" => "Venue required"]);
	exit;
}

try {
	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

	// the type is hardcoded here so the browser can't pick which listener function runs
	$request = [
		"type"         => "rateVenue",
		"venue"        => $venue,
		"city"         => $city,
		"sessionToken" => $token,
		"clicked"      => $clicked,
		"rating"       => $rating
	];

	$response = $client->send_request($request);

	if (is_array($response) && ($response["success"] ?? false) === true) {
		$out = ["success" => true];
		// average only exists once at least one rating has been saved
		if (isset($response["average"])) {
			$out["average"] = $response["average"];
		}
		echo json_encode($out);
	} else {
		echo json_encode([
			"success" => false,
			"message" => (is_array($response) ? ($response["message"] ?? null) : null) ?? "Could not load venue"
		]);
	}
} catch (Throwable $e) {
	error_log("Venue handler error: " . $e->getMessage());
	echo json_encode(["success" => false, "message" => "Unable to reach backend"]);
}

?>
