<?php

use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init();

$pdo = database();
$stmt = $pdo->prepare('SELECT `id`, `name` FROM Room');
$stmt->execute();
$rooms = $stmt->fetchAll();
echo "[";
$first = true;
foreach ($rooms as $room) {
    if ($first) {
        $first = false;
    } else {
        echo ",";
    }
    echo "{";
    echo "\"id\":";
    echo $room['id'];
    echo ",";
    echo "\"name\":";
    echo "\"";
    echo $room['name'];
    echo "\"";
    echo "}";
}
echo "]";
