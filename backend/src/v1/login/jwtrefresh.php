<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

$user = init(Permission::LOGIN);
ok(['jwt' => $user->createJwt()]);