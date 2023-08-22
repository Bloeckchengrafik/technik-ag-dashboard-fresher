<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\AuthKey;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email)) {
    error('Bitte alle Felder ausfüllen');
}

$user = User::fromEmail($body->email);
if (!$user) {
    error('Dieser Benutzer existiert nicht');
}

// Check if the user has a password
$authKeys = AuthKey::fromUser($user);
$hasPassword = false;
foreach ($authKeys as $authKey) {
    if ($authKey->method === AuthKey::$METHOD_EMAIL) {
        $hasPassword = true;
        break;
    }
}

if ($hasPassword) {
    error('Dieser Benutzer hat ein Passwort');
}

try {
    $user->sendConfirmationEmail();
} catch (Exception $e) {
    error('Die E-Mail konnte nicht gesendet werden');
}

ok([]);
