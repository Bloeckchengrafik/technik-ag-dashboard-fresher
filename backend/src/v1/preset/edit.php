<?php

use Modules\Event\Preset;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

init(Permission::EditPresets);

$body = json_decode(file_get_contents('php://input'));

if (!isset($body->name) || !isset($body->id)) {
    error("Missing name");
}

$preset = Preset::byId(intval($body->id));
$preset->tech = $body->name;
$preset->save();
ok([]);