<?php

include_once "../../Modules/Autoload.php";

use Modules\Login\Group;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Api\map;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

$user = init(Permission::UserAdmin);
$pdo = database();

$stmt = $pdo->prepare("SELECT * FROM `Permission`");
$stmt->execute();

ok($stmt->fetchAll());
