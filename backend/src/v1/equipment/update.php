<?php

use Modules\Equipment\Equipment;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::EquipmentChange);

$body = json_decode(file_get_contents('php://input'));

if (!isset($body->id)) {
    error('Fill all fields');
}

$equipment = Equipment::fromId(intval($body->id));
if (!$equipment) {
    error('Equipment not found');
}

if (isset($body->name)) {
    $equipment->name = $body->name;
}

if (isset($body->count)) {
    $equipment->count = intval($body->count);
}

if (isset($body->category_id)) {
    $equipment->category_id = intval($body->category_id);
}

if (isset($body->location_id)) {
    $equipment->location_id = intval($body->location_id);
}

if (isset($body->manufacturer_id)) {
    $equipment->manufacturer_id = intval($body->manufacturer_id);
}

if (isset($body->description)) {
    $equipment->description = $body->description;
}

$equipment->save();
ok([]);
