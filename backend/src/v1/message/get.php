<?php

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

$user = init(Permission::Login);
$db = database();

$messages = $db->prepare("SELECT message FROM Message WHERE user_id = ?");
$messages->execute([$user->id]);
$msg = $messages->fetchAll();
ok($msg);