<?php
include_once '../../Modules/Autoload.php';

use Modules\Email\Mailer;
use Modules\Login\AuthKey;
use Modules\Login\Permission;
use Modules\Login\StudentInfo;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

$user = init(Permission::Login);

$body = json_decode(file_get_contents('php://input'));

if (!isset($body->year) || !isset($body->tutor)) {
    error('Jahrgang und Tutor sind erforderlich');
}

StudentInfo::create($user, $body->year, $body->tutor);

ok([]);
