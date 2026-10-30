<?php
// Waits on emailQueue through RabbitMQ for email jobs and sends
// them through the team Gmail account using PHPMailer.

// The message it expects looks like:
// type => "send_email"
// template => "pick_confirmation" or "event_reminder"
// to => the user's email address
// event => name, date, venue, city, url

// PHPMailer's classes have long full names. "use" lets us
// just write PHPMailer and Exception below.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer. Composer created vendor/autoload.php when we installed it.
// __DIR__ pretty much means "the folder this file is in" (dmz/email).
require __DIR__ . '/vendor/autoload.php';

// The RabbitMQ library and testRabbitMQ.ini are at the top of the repo,
// two folders up. The library opens its files using paths relative to
// the current folder, so we move up there first.
chdir(__DIR__ . '/../..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Read the Gmail address and app password from a file which is outside the repo,
// so the password never ends up on GitHub.
$config = parse_ini_file('/etc/eventplanner/email.ini');

// If the file is missing or incomplete, stop now with a clear message
// instead of failing on every email later
if ($config === false || empty($config['SMTP_USER']) || empty($config['SMTP_PASS'])) {
    die("Could not read Gmail settings from /etc/eventplanner/email.ini\n");
}


// Sends one email through Gmail.
// Returns true if Gmail accepted it, false if something went wrong.
function sendEmail($config, $to, $subject, $body) {
    // "true" tells PHPMailer to throw an error we can catch if anything fails
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP(); // send through a mail server (Gmail)
        $mail->Host = 'smtp.gmail.com'; // Gmail's outgoing mail server
        $mail->SMTPAuth = true; // Gmail requires us to log in
        $mail->Username = $config['SMTP_USER']; // the team Gmail address
        $mail->Password = $config['SMTP_PASS']; // the app password (not the real one)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // encrypt the connection
        $mail->Port       = 587;                            // Gmail's port for encrypted sending

        $mail->setFrom($config['SMTP_USER'], $config['FROM_NAME'] ?? 'IT490 Events');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body; // plain text email

        $mail->send();
        return true;
    } catch (Exception $e) {
 // Save the real error in the server's log, then report failure
        error_log("Email to $to failed: " . $mail->ErrorInfo);
        return false;
    }
}


// Cleans one piece of event text before we put it in an email:
// 1. makes sure it's text, removes line breaks (so it can't mess up the subject line),
// trims extra spaces, cuts it to a maximum length
function cleanText($value, $maxLength) {
    $text = (string) $value;
    $text = str_replace(["\r", "\n"], ' ', $text);
    $text = trim($text);
    return substr($text, 0, $maxLength);
}


// Builds the email's subject and body from a template name and event details.
// Only these two templates exist, so nobody can send their own email text
// through our queue (which could let someone use it to send spam).
// Returns an array with "subject" and "body", or null if the template is unknown.
function buildEmail($template, $event) {
    // Clean each detail, with a fallback if it's missing
    $name  = cleanText($event['name'] ?? 'your event', 200);
    $date  = cleanText($event['date'] ?? 'TBA', 50);
    $venue = cleanText($event['venue'] ?? 'TBA', 200);
    $city  = cleanText($event['city'] ?? '', 100);
    $url   = cleanText($event['url'] ?? '', 500);

    // Where the event is, e.g. "Madison Square Garden, New York"
    $place = $venue;
    if ($city !== '') {
        $place = $venue . ", " . $city;
    }

    // Only include a ticket link if it's a normal https web address
    $ticketLine = "";
    if (str_starts_with($url, 'https://')) {
        $ticketLine = "\nTickets: " . $url . "\n";
    }

    if ($template === 'pick_confirmation') {
        return [
            "subject" => "You're going: " . $name,
            "body"    => "You saved an event!\n\n" . $name . "\nDate: " . $date . "\nWhere: " . $place . "\n" . $ticketLine . "\nWe'll remind you the day before.\n\n- IT490 Events"
        ];
    }

    if ($template === 'event_reminder') {
        return [
            "subject" => "Reminder: " . $name . " is tomorrow",
            "body"    => "Just a reminder that your event is coming up.\n\n" . $name . "\nDate: " . $date . "\nWhere: " . $place . "\n" . $ticketLine . "\nHave fun!\n\n- IT490 Events"
        ];
    }

    return null;   // the template name wasn't one we know
}


// The RabbitMQ library calls this every time a message arrives on emailQueue.
// Whatever we return gets sent back to whoever sent the message.
function requestProcessor($request) {
    // "global" lets this function use the $config variable we created
    // at the top of the file, outside of any function
    global $config;

    echo "Received email job" . PHP_EOL;

    // Only accept send_email messages
    if (!is_array($request) || ($request['type'] ?? '') !== 'send_email') {
        return ["success" => false, "message" => "Invalid request type"];
    }

    // The recipient must look like a real email address
    $to = $request['to'] ?? '';
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ["success" => false, "message" => "Invalid email address"];
    }

    // The event details need to be an array like name, date, venue, etc.
    $event = $request['event'] ?? [];
    if (!is_array($event)) {
        return ["success" => false, "message" => "Invalid event details"];
    }

    // Build the email from the template. Unknown templates are rejected.
    $email = buildEmail($request['template'] ?? '', $event);
    if ($email === null) {
        return ["success" => false, "message" => "Unknown email template"];
    }

    // Send it, and report back whether it worked
    if (sendEmail($config, $to, $email["subject"], $email["body"])) {
        echo "Sent '" . $email["subject"] . "' to " . $to . PHP_EOL;
        return ["success" => true, "message" => "Email sent"];
    }

    return ["success" => false, "message" => "Email could not be sent"];
}


// Start listening. "emailServer" picks the [emailServer] section of
// testRabbitMQ.ini, so we listen on emailQueue, not the database's testQueue.
$server = new rabbitMQServer("testRabbitMQ.ini", "emailServer");
echo "Email listener started..." . PHP_EOL;
$server->process_requests("requestProcessor");
