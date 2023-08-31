<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Group;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Api\map;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);

ok(map(Group::all(), function (Group $grp) {
    return $grp->toArray();
}));