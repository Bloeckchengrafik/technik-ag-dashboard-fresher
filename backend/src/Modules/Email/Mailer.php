<?php

namespace Modules\Email;



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use function Modules\Utils\database;

class Mailer
{

    static function send($to, $subject, $body): void
    {
        $conn = database();

        $stmt = $conn->prepare("INSERT INTO Email (receiver, subject, content) VALUES (:to, :subject, :body)");
        $stmt->bindParam(":to", $to);
        $stmt->bindParam(":subject", $subject);
        $stmt->bindParam(":body", $body);

        $stmt->execute();

        $id = $conn->lastInsertId();
        ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
        Mailer::sendId($id);

        //mail($to, $subject, $body, "From: " . $GLOBALS["config"]["mail"]["from"]);
    }

    /**
     * @throws Exception
     */
    static function sendId($id): string
    {
        $conn = database();

        $stmt = $conn->prepare("SELECT * FROM Email WHERE email_id = :mail_id");
        $stmt->bindParam(":mail_id", $id);
        $stmt->execute();

        $result = $stmt->fetch();

        if (!$result) {
            return "Mail not found";
        }

        $to = $result["receiver"];
        $subject = $result["subject"];
        $body = $result["content"];

        $stmt = $conn->prepare("DELETE FROM Email WHERE email_id = :mail_id");
        $stmt->bindParam(":mail_id", $id);
        $stmt->execute();

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $GLOBALS["config"]["mail"]["host"];
        $mail->SMTPAuth = true;
        $mail->Username = $GLOBALS["config"]["mail"]["username"];
        $mail->Password = $GLOBALS["config"]["mail"]["password"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $GLOBALS["config"]["mail"]["port"];

        $mail->setFrom($GLOBALS["config"]["mail"]["from"], $GLOBALS["config"]["mail"]["fromName"]);
        $mail->addAddress($to);

        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $body;

        if ($mail->send()) {
            return "Mail sent. ". $mail->ErrorInfo;
        } else {
            return "Mail not sent, " . $mail->ErrorInfo . "\n";
        }
    }
}