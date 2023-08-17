<?php
include_once __DIR__ . '/../../Modules/Autoload.php';

use Modules\Email\Mailer;

// Create a log file
if (!file_exists(__DIR__ . "/mail.log")) {
    $file = fopen(__DIR__ . "/mail.log", "w");
    fwrite($file, "Mail log created at " . date("d.m.Y H:i:s") . "\n");
    fclose($file);
}

function logMail($message): void
{
    $file = fopen(__DIR__ . "/mail.log", "a");
    fwrite($file, $message . "\n");
    fclose($file);
}

$id = $argv[1];
$idNum = intval($id);

logMail("Sending mail $idNum");
try {
    $ans = Mailer::sendId($idNum);
    logMail("Sent mail $idNum ($ans)");
} catch (\PHPMailer\PHPMailer\Exception $e) {
    logMail("Failed to send mail $idNum");
    logMail($e->getMessage());
}