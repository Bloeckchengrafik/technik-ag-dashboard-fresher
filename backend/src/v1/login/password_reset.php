<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email)) {
    error('Bitte alle Felder ausfüllen');
}

if (!filter_var($body->email, FILTER_VALIDATE_EMAIL)) {
    error('Bitte eine gültige E-Mail-Adresse angeben');
}

$user = User::fromEmail($body->email);
if (!$user) {
    ok([]); // do nothing
}

try {
    $user->sendPasswordResetEmail();
} catch (Exception $e) {
    error('Fehler beim Senden der E-Mail');
}

ok([]);