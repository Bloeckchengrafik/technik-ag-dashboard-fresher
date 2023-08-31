<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Group;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);
$body = json_decode(file_get_contents('php://input'));

if (!isset($body->name)) {
    error("Missing name");
}

$group = Group::create($body->name);
ok($group->toArray());