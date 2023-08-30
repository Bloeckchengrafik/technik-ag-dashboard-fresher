<?php

use Modules\Login\Permission;
use Modules\Login\StudentInfo;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

include_once '../../../Modules/Autoload.php';

$user = init(Permission::Login);
$body = json_decode(file_get_contents('php://input'));

$studentInfo = StudentInfo::fromUser($user);
if ($studentInfo == null) ok(["needs_update" => false]);
else ok(["needs_update" => $studentInfo->needsUpdate()]);