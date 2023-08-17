<?php
include_once '../Modules/Autoload.php';

use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

init();

// Just give a dummy jwt

$user = new User(1, 'John', 'Doe', 'jd@dev.to', '***', 1);
ok(['jwt' => $user->createJwt()]);