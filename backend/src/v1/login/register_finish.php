<?php
include_once '../../Modules/Autoload.php';

use Modules\Email\Mailer;
use Modules\Login\AuthKey;
use Modules\Login\Permission;
use Modules\Login\StudentInfo;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email) && !isset($body->key) && !isset($body->password)) {
    error('Email is required');
}

$user = User::fromEmail($body->email);
if (!$user) {
    error('Dieser Benutzer existiert nicht');
}

$keyValidate = function (string $key) use ($body) {
    return $key === $body->key;
};

$works = $user->useKey($keyValidate, AuthKey::$METHOD_REGISTRATION);

if (!$works) {
    error('Dieser Code ist ungültig');
}

AuthKey::create($user, password_hash($body->password, PASSWORD_DEFAULT), AuthKey::$METHOD_EMAIL, false);

$user->permissions[] = Permission::Login->value;
$user->permissions[] = Permission::ShowAsUser->value;
$user->save();

ok([
    'jwt' => $user->createJwt(),
]);
