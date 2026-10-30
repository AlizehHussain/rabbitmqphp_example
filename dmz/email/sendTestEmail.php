<?php
// Bring in PHPMailer's classes by their full names so we can write "PHPMailer" below
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Composer's autoloader loads PHPMailer's files automatically when we use them
require __DIR__ . '/vendor/autoload.php';

// Read the Gmail login from the config file outside the repo, so it never goes to GitHub
$config = parse_ini_file('/etc/eventplanner/email.ini');
if ($config === false) {
    die("Could not read /etc/eventplanner/email.ini\n");
}

// The recipient comes from the command line: php sendTestEmail.php you@example.com
$to = $argv[1] ?? '';
// Make sure it's a real email address format before trying to send
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    die("Usage: php sendTestEmail.php you@example.com\n");
}

// "true" makes PHPMailer throw an exception on errors so we can catch them
$mail = new PHPMailer(true);

try {
    // Send through an SMTP server like gmail instead of the VM's own mail system
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail's outgoing mail server
    $mail->SMTPAuth = true; // Gmail requires logging in
    $mail->Username = $config['SMTP_USER']; // the team Gmail address
    $mail->Password = $config['SMTP_PASS']; // the app password, not the real password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // encrypt the connection
    $mail->Port = 587; // Gmail's port for STARTTLS

    // Who the email is from and who it goes to
    $mail->setFrom($config['SMTP_USER'], $config['FROM_NAME'] ?? 'IT490 Events');
    $mail->addAddress($to);

    // Message that comes from email
    $mail->Subject = 'Test email from IT490';
    $mail->Body    = "If you're reading this, our email system works.";

    // Actually send it
    $mail->send();
    echo "Sent to $to\n";
} catch (Exception $e) {

    echo "Failed: {$mail->ErrorInfo}\n";
}
