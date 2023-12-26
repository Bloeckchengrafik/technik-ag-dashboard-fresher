<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

$user = init(Permission::Login);
$user = User::fromEmail($user->email);
ok(['jwt' => $user->createJwt()]);