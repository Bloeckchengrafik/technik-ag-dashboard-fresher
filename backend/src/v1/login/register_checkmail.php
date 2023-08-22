<?php
include_once '../../Modules/Autoload.php';

use Modules\Email\Mailer;
use Modules\Login\AuthKey;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init();

$body = json_decode(file_get_contents('php://input'));
if (!isset($body->email)) {
    error('Email is required',);
}

$user = User::fromEmail($body->email);
if (!$user) {
    ok([
        "userExists" => false,
        "needsCompleteRegistration" => false
    ]);
}

$authKeys = AuthKey::fromUser($user);
$needsCompleteRegistration = false;
foreach ($authKeys as $authKey) {
    if ($authKey->method === AuthKey::$METHOD_REGISTRATION) {
        $needsCompleteRegistration = true;
        break;
    }
}


ok([
    "userExists" => true,
    "needsCompleteRegistration" => $needsCompleteRegistration
]);