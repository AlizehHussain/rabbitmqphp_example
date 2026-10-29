<?php
header("Content-Type: application/json"); //we tell the brwoser were returning json data instead of html, easier for the javascript to read

require_once __DIR__ . "/path.inc";
require_once __DIR__ . "/get_host_info.inc";
require_once __DIR__ . "/rabbitMQLib.inc";

//THIS FILE ALLOWS THE FRONTEND TO RETRIEVE TICKETMASTER EVENTS FROM THE DATABASE :)

if ($_SERVER["REQUEST_METHOD"] !== "GET"){
	http_response_code(405); //response 405 means the request method isnt allowed

	echo json_encode([
	"success" => false,
	"message" => "GET method required"
	]);

	exit;
}

try{
	$client = new rabbitMQClient(
		__DIR__ . "/testRabbitMQ.ini",
		"testServer"
	);

	$request = [
		"type" => "get_events"
	];

	$response = $client->send_request($request);

	if (!is_array($response)){
		http_response_code(502); //502 is for an invalid response from the backend

		echo json_encode([
			"success" => false,
			"message" => "Something wrong with backend"
		]);

		exit;
	}

	if (($response["success"] ?? false) !== true){
		http_response_code(502);
	}

	echo json_encode($response);
} catch (Throwable $e){
	error_log("Get events error: " . $e->getMessage());

	http_response_code(502);

	echo json_encode([
		"success" => false,
		"message" => "Unable to retrieve events"
	]);
}

?>
