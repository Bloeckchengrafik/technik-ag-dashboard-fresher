<?php

use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

init();

$db = database();
$stmt = $db->prepare('SELECT Preset.id, Preset.tech, count(distinct event_id) as popularity FROM Preset LEFT JOIN EventPreset EP on Preset.id = EP.preset_id GROUP BY Preset.id');
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
ok($rooms);