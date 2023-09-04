<?php

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init(Permission::EquipmentChange);

$id = $_GET['id'];
if (!$id) {
    error('id is required');
}

$pdo = database();
$pdo->prepare('DELETE FROM Equipment WHERE category_id = ?')->execute([$id]);
$pdo->prepare('DELETE FROM EquipmentCategory WHERE id = ?')->execute([$id]);
ok([]);