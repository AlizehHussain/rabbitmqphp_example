<?php
// emailListener.php
// Waits on emailQueue (through RabbitMQ) for email jobs and
// sends them through the team Gmail account using PHPMailer.
// Runs on the DMZ machine, since that's the only one allowed
// to talk to the outside internet (Gmail).

// Bring in PHPMailer's classes by their full names
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer. __DIR__ is this file's folder (dmz/email)
require __DIR__ . '/vendor/autoload.php';

// The RabbitMQ library and its config files live at the top of the repo,
// two folders up. The library opens host.ini using a relative path, so we
// move into that folder first or it won't find its files.
chdir(__DIR__ . '/../..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Read the Gmail login from outside the repo, so the password never goes to GitHub.
// Done once at startup: if it's missing, stop right away instead of failing on every email.
$config = parse_ini_file('/etc/eventplanner/email.ini');
if ($config === false || empty($config['SMTP_USER']) || empty($config['SMTP_PASS'])) {
    die("Could not read Gmail settings from /etc/eventplanner/email.ini\n");
}

// Sends one email. Returns true if Gmail accepted it, false if not.
function sendEmail($to, $subject, $body) {
    global $config;                     // use the Gmail settings loaded above

    $mail = new PHPMailer(true);        // true = throw exceptions on errors
    try {
        $mail->isSMTP();                                  // send through an SMTP server
        $mail->Host       = 'smtp.gmail.com';             // Gmail's outgoing mail server
        $mail->SMTPAuth   = true;                         // Gmail requires a login
        $mail->Username   = $config['SMTP_USER'];         // team Gmail address
        $mail->Password   = $config['SMTP_PASS'];         // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // encrypt the connection
        $mail->Port       = 587;                          // Gmail's STARTTLS port

        $mail->setFrom($config['SMTP_USER'], $config['FROM_NAME'] ?? 'IT490 Events');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body;                           // plain text email

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the real reason on the server; the caller just gets false
        error_log("Email to $to failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Cleans one piece of event text before it goes into an email:
// forces it to a string, removes line breaks (which could mess up the
// subject line), trims spaces, and cuts it to a reasonable length.
function clean($value, $maxLength = 200) {
    $text = str_replace(["\r", "\n"], ' ', (string) $value);
    return substr(trim($text), 0, $maxLength);
}

// Builds the subject and body from a template name and event details.
// Only these templates exist, so nobody can push their own email
// content through our queue (which would let it be abused for spam).
function buildEmail($template, $event) {
    $name  = clean($event['name'] ?? 'your event');
    $date  = clean($event['date'] ?? 'TBA', 50);
    $venue = clean($event['venue'] ?? 'TBA');
    $city  = clean($event['city'] ?? '', 100);
    $url   = clean($event['url'] ?? '', 500);

    // Venue plus city if we have one, e.g. "Madison Square Garden, New York"
    $place = $city !== '' ? "$venue, $city" : $venue;

    // Only include the ticket link if it's a real https address
    $ticketLine = str_starts_with($url, 'https://') ? "\nTickets: $url\n" : '';

    if ($template === 'pick_confirmation') {
        return [
            "You're going: $name",
            "You saved an event!\n\n$name\nDate: $date\nWhere: $place\n$ticketLine\nWe'll remind you the day before.\n\n- IT490 Events"
        ];
    }
    if ($template === 'event_reminder') {
        return [
            "Reminder: $name is tomorrow",
            "Just a reminder that your event is coming up.\n\n$name\nDate: $date\nWhere: $place\n$ticketLine\nHave fun!\n\n- IT490 Events"
        ];
    }
    return null;   // unknown template
}

// Called by the RabbitMQ library every time a message arrives on emailQueue.
// Whatever this returns gets sent back to whoever sent the message.
function requestProcessor($request) {
    echo "Received email job" . PHP_EOL;

    // Only accept send_email messages
    if (!is_array($request) || ($request['type'] ?? '') !== 'send_email') {
        return ["success" => false, "message" => "Invalid request type"];
    }

    // The recipient must be a real email address format
    $to = $request['to'] ?? '';
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ["success" => false, "message" => "Invalid email address"];
    }

    // Event details must be an array (name, date, venue, ...)
    $event = $request['event'] ?? [];
    if (!is_array($event)) {
        return ["success" => false, "message" => "Invalid event details"];
    }

    // Build the email from the template; unknown templates are rejected
    $email = buildEmail($request['template'] ?? '', $event);
    if ($email === null) {
        return ["success" => false, "message" => "Unknown email template"];
    }
    [$subject, $body] = $email;   // split into subject and body

    // Send it and report back
    if (sendEmail($to, $subject, $body)) {
        echo "Sent '$subject' to $to" . PHP_EOL;
        return ["success" => true, "message" => "Email sent"];
    }
    return ["success" => false, "message" => "Email could not be sent"];
}

// Start listening on emailQueue, using the [emailServer] section of the .ini
$server = new rabbitMQServer("testRabbitMQ.ini", "emailServer");
echo "Email listener started..." . PHP_EOL;
$server->process_requests("requestProcessor");
