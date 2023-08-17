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
        self::launchBackgroundProc("php " . __DIR__ . "/sendmail.php " . $id);

    }

    private static function launchBackgroundProc($command): void
    {
        if(PHP_OS=='WINNT' || PHP_OS=='WIN32' || PHP_OS=='Windows'){
            $command = 'start "" '. $command;
        } else {
            $command = $command .' /dev/null &';
        }
        $handle = popen($command, 'r');
        if($handle!==false) {
            pclose($handle);
        }
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