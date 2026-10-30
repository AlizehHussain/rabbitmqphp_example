<?php

require_once __DIR__ . '/../../path.inc';
require_once __DIR__ . '/../../get_host_info.inc';
require_once __DIR__ . '/../../rabbitMQLib.inc';

//This is in my(nick) local dir, dw
$file = "/opt/eventplanner/events.json";

if (!file_exists($file)){
	die("Error: events.json not fount\n");
}

//----------------------------------------------------

$json = file_get_contents($file);
$data = json_decode($json, true); //this turns the contents into a php array, makes it easier to extract individual event details

if (!is_array($data) || !isset($data["_embedded"]["events"])){
	die("Error: Invalid Ticketmaster data\n");
}

$events = [];

//this loops through each ticketmaster event
//ticketmaster basically gives us more info then needed so i grab the useful stuff
//?? "" is a fallback just incase theres somethiung missing
foreach ($data["_embedded"]["events"] as $event){
	if (empty($event["id"]) || empty($event["name"])){
		continue;
	}

	$events[] = [
		"event_id" => $event["id"],
		"name" => $event["name"],
		"date" => $event["dates"]["start"]["localDate"] ?? null,
		"venue" => $event["_embedded"]["venues"][0]["name"] ?? "",
		"city" => $event["_embedded"]["venues"][0]["city"]["name"] ?? "",
		"category" => $event["classifications"][0]["segment"]["name"] ?? "",
		"url" => $event["url"] ?? ""
		];
}

echo "Events prepared: " . count($events) . PHP_EOL;

//Now gonna stop this if there are no valid events

if (count($events) === 0){
	exit("No events to publish\n");
}

//Time to send it.
$client = new rabbitMQClient(
	__DIR__ . '/../../testRabbitMQ.ini',
	"testServer"
);

//Sending events in small batches
//array_chunk basically splits an array into pieces, therefore each piece repesents 1 piece of data(1 event)
//i did this so we don't overload with too much data
foreach (array_chunk($events, 5) as $batch){
	$request = [
		"type" => "sync_events",
		"events" => $batch
	];
	echo "Sending to rabbitmq" . PHP_EOL;
	$response = $client->send_request($request);
	echo "Response received" . PHP_EOL;
	var_dump($response);
	if (!is_array($response) || ($response["success"] ?? false) !== true){
		//prints error to standard error
		fwrite(STDERR, "Event batch synchronization failed\n");
		exit(1); //stops the script with a failure code
	}

	echo "Sent out batch of " . count($batch) . " events \n";
}

echo "Event synchronization completed\n";

//WHAT THIS SCRIPT DOES
//Opens my local events file
//Reads like 100 ticketmaster events
//extracts info (IDs, names, dates, venues, categories, and urls)
//Splits the events into batches of 5
//sends each to rabbitmq using type = sync_events
//it waits until a successful response before sending out the next batch (like a boss)

?>
