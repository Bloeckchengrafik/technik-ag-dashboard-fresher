<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);

if (!isset($_GET['id'])) {
    error("Missing id");
}

$user = User::fromId($_GET['id']);
if ($user == null) {
    error("User not found");
}

ok($user->toArray());