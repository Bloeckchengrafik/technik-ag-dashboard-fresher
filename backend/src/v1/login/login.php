<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\AuthKey;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email) || !isset($body->password)) {
    error('Bitte alle Felder ausfüllen');
}

$user = User::fromEmail($body->email);
if (!$user) {
    error('Benutzer nicht gefunden');
}

$works = $user->useKey(function (string $other) use($body) { return password_verify($body->password, $other); }, AuthKey::$METHOD_EMAIL);

if (!$works) {
    error('Passwort falsch');
}

ok(['jwt' => $user->createJwt()]);
