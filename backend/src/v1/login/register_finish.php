<?php
include_once '../../Modules/Autoload.php';

use Modules\Email\Mailer;
use Modules\Login\AuthKey;
use Modules\Login\StudentInfo;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email) && !isset($body->key) && !isset($body->password)) {
    error('Email is required');
    exit();
}

$user = User::fromEmail($body->email);
if (!$user) {
    error('Dieser Benutzer existiert nicht');
    exit();
}

$works = $user->useKey($body->key, AuthKey::$METHOD_REGISTRATION);

if (!$works) {
    error('Dieser Code ist ungültig');
    exit();
}

AuthKey::create($user, password_hash($body->password, PASSWORD_DEFAULT), AuthKey::$METHOD_EMAIL, false);

if (!isset($body->studentInfo)) {
    ok([
        'jwt' => $user->createJwt(),
    ]);
    exit();
}

$studentInfo = $body->studentInfo;
if (!isset($studentInfo->year) || !isset($studentInfo->tutor)) {
    error('Jahrgang und Tutor sind erforderlich');
    exit();
}

StudentInfo::create($user, $studentInfo->year, $studentInfo->tutor);

ok([
    'jwt' => $user->createJwt(),
]);
