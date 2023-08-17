<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email) || !isset($body->firstname) || !isset($body->lastname)) {
    error('Bitte alle Felder ausfüllen');
    exit();
}

$user = User::fromEmail($body->email);
if ($user) {
    error('Dieser Benutzer existiert bereits');
    exit();
}

$user = User::create($body->firstname, $body->lastname, $body->email);
try {
    $user->sendConfirmationEmail();
} catch (Exception $e) {
    error('Fehler beim Senden der E-Mail');
    exit();
}

ok([]);
