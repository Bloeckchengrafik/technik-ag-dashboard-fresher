<?php
include_once '../../../Modules/Autoload.php';

use Modules\Login\Permission;
use Modules\Login\StudentInfo;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::Login);
$info = StudentInfo::fromUser($user);

if ($info == null) {
    error("Not a student");
}

ok($info->toArray());