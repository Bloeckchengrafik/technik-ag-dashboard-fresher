<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Group;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);

if (!isset($_GET['id'])) {
    error("Missing id");
}

$group = Group::byId($_GET['id']);

if ($group === null) {
    error("Group not found");
}

ok($group->resolveToArray());