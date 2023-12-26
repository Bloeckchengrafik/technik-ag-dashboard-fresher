<?php

use Modules\Equipment\Equipment;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::EquipmentChange);

$body = json_decode(file_get_contents('php://input'));

Equipment::create(
    $body->name,
    intval($body->count),
    intval($body->category_id),
    intval($body->location_id),
    intval($body->manufacturer_id),
    $body->description
);
ok([]);
