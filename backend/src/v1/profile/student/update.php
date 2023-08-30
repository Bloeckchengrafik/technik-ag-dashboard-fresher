<?php

use Modules\Login\Permission;
use Modules\Login\StudentInfo;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;
include_once '../../../Modules/Autoload.php';

$user = init(Permission::Login);

$body = json_decode(file_get_contents('php://input'));

if (!isset($body->isStudent)) {
    error("Missing isStudent");
}

if (!$body->isStudent) {
    StudentInfo::fromUser($user)->delete();
    ok([]);
}

if (!isset($body->year) || !isset($body->tutor)) {
    error("Missing year or tutor");
}

$year = intval($body->year);
$tutor = $body->tutor;

$studentinfo = StudentInfo::fromUser($user);
if ($studentinfo == null) {
    $studentinfo = StudentInfo::create($user, $year, $tutor);
} else {
    $studentinfo->year = $year;
    $studentinfo->tutor = $tutor;
    $studentinfo->save();
}

ok([]);