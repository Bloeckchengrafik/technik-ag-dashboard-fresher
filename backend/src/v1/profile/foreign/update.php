<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

init(Permission::UserAdmin);
$body = json_decode(file_get_contents('php://input'));

if (!isset($_GET['id']) || !isset($body->permissions) || !isset($body->groups)) {
    error("Missing fields");
}

$user = User::fromId($_GET['id']);
if ($user == null) {
    error("User not found");
}

$user->permissions = $body->permissions;
$user->groups = $body->groups;
$user->save();
ok([]);