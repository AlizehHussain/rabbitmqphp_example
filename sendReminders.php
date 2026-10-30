<?php
// Emails a reminder for every saved event that happens in the next 24 hours.
// It runs on a schedule (cron), not from a web page.
// The reminder_sent column makes sure each saved event only gets ONE reminder.
//
// Needs this column first (run once in MySQL):
//   ALTER TABLE UserPicks ADD reminder_sent TINYINT NOT NULL DEFAULT 0;

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// db_connection.php creates $connection (password comes from the DB_PASSWORD variable)
require('db_connection.php');

// Find saved events happening in the next 24 hours that haven't been reminded yet,
// along with the user's email and the event details
$result = $connection->query(
	"SELECT p.UserID, p.EventID, u.email, e.EventName, e.EventDate, e.Venue, e.City, e.TicketURL
	FROM UserPicks p
	JOIN users u ON u.uid = p.UserID
	JOIN Events e ON e.EventID = p.EventID
	WHERE p.reminder_sent = 0
	AND e.EventDate BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)"
);

// One connection to the email queue for all the reminders
$client = new rabbitMQClient("testRabbitMQ.ini", "emailServer");

// After each email, mark that pick as reminded so it's never sent again
$mark = $connection->prepare(
	"UPDATE UserPicks SET reminder_sent = 1 WHERE UserID = ? AND EventID = ?"
);

$count = 0;
while ($row = $result->fetch_assoc()){
	// Same message format as the save email, just a different template
	$client->publish([
		"type"     => "send_email",
		"template" => "event_reminder",
		"to"       => $row["email"],
		"event"    => [
			"name"  => $row["EventName"],
			"date"  => $row["EventDate"],
			"venue" => $row["Venue"],
			"city"  => $row["City"],
			"url"   => $row["TicketURL"]
		]
	]);

	$mark->bind_param("is", $row["UserID"], $row["EventID"]);
	$mark->execute();
	$count++;
}

$mark->close();
$connection->close();

echo "Sent $count reminder(s)" . PHP_EOL;
