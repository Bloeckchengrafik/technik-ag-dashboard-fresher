<?php

use Modules\Event\Event;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

$user = init(Permission::Login);

ok(Event::byOrganizerId($user->id));

