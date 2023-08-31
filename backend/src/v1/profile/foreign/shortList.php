<?php

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Api\map;
use function Modules\Utils\Json\ok;

include_once "../../../Modules/Autoload.php";

init(Permission::UserAdmin);

$users = User::all();

ok(map($users, function (User $user) {
    $map = $user->toArray();
    $map['isStudent'] = $user->isStudent();
    return $map;
}));
