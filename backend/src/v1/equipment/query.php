<?php

use Modules\Equipment\CategoryCategorizer;
use Modules\Equipment\Equipment;
use Modules\Equipment\NameCategorizer;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::EquipmentView);

$tab = intval($_GET['tab'] ?? '0');
$equipment = Equipment::all();

$categorizer = new CategoryCategorizer();
if ($tab === 1) {
    $categorizer = new NameCategorizer();
}

$equipment = $categorizer->categorize($equipment);

ok($equipment);