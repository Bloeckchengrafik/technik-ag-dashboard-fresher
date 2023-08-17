<?php
include_once '../../Modules/Autoload.php';

use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

$user = init(1);
ok(['jwt' => $user->createJwt()]);