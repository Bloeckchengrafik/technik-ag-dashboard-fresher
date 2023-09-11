<?php

use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

init(Permission::UserAdmin);
$db = database();
$body = json_decode(file_get_contents('php://input'));

$user_id = $body->user_id;
$message = $body->message;

$add = $db->prepare("INSERT INTO Message (user_id, message) VALUES (?, ?)");
$add->execute([$user_id, $message]);

ok([]);