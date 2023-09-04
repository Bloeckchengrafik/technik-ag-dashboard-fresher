<?php

use Modules\Event\Preset;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

init(Permission::EditPresets);

$id = $_GET["id"];
if (!isset($id)) {
    error("Missing id");
}

$pdo = database();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM `EventPreset` WHERE `preset_id` = ? LIMIT 1");
$stmt->execute([intval($id)]);
if ($stmt->fetchColumn() > 0) {
    error("Preset wird aktuell in einer Veranstaltung verwendet");
}

Preset::byId(intval($id))->delete();
ok([]);