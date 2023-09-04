<?php

use Modules\Equipment\Equipment;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::UserAdmin);

$id = $_GET["id"];
$equip = Equipment::fromId($id);

if (!$equip) {
    error("Equipament not found");
}

$equip->delete();
ok([]);