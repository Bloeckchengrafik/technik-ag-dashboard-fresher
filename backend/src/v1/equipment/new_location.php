<?php

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::EquipmentChange);

$name = json_decode(file_get_contents('php://input'))->name;
if (!$name) {
    error('Name is required');
}

$pdo = database();
$pdo->prepare('INSERT INTO EquipmentLocation (name) VALUES (?)')->execute([$name]);

ok([]);