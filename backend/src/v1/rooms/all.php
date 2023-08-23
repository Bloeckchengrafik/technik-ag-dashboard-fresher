<?php

use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init();

$db = database();
$stmt = $db->prepare('SELECT * FROM Room');
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
ok($rooms);















