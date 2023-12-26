<?php

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

$user = init();
$db = database();

$del = $db->prepare("DELETE FROM Message WHERE user_id = ?");
$del->execute([$user->id]);

ok([]);
