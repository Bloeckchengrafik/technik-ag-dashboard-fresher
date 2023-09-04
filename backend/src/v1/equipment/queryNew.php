<?php

use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init();
$pdo = database();

$categories = $pdo->query('SELECT * FROM EquipmentCategory')->fetchAll(PDO::FETCH_ASSOC);
$locations = $pdo->query('SELECT * FROM EquipmentLocation')->fetchAll(PDO::FETCH_ASSOC);
$manufacturers = $pdo->query('SELECT * FROM EquipmentManufacturer')->fetchAll(PDO::FETCH_ASSOC);

ok([
    'categories' => $categories,
    'locations' => $locations,
    'manufacturers' => $manufacturers
]);