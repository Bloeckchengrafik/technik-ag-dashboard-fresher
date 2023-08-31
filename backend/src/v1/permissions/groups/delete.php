<?php

include_once "../../../Modules/Autoload.php";

use Modules\Login\Group;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);
$body = json_decode(file_get_contents('php://input'));

if (!isset($body->id)) {
    error("Missing id");
}

$group = Group::byId($body->id);
if (!$group) {
    error("Group not found");
}

$group->delete();

ok([]);