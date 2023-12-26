<?php

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../../Modules/Autoload.php";

init(Permission::UserAdmin);

if (!isset($_GET['id'])) {
    error("Missing id");
}

$user = User::fromId(intval($_GET['id']));
if ($user === null) {
    error("User not found");
}

$pdo = database();

$own_events_stmt = $pdo->prepare("SELECT COUNT(*) FROM Event WHERE organizer_id = ?");
$own_events_stmt->execute([$user->id]);
$own_events = $own_events_stmt->fetchColumn();

$participated_events_stmt = $pdo->prepare(<<<SQL
SELECT COUNT(DISTINCT Event.id) FROM Event, Shift, UserShift
WHERE Shift.id = UserShift.shift_id
  AND Shift.event_id = `Event`.id
  AND UserShift.user_id = ?;
SQL);
$participated_events_stmt->execute([$user->id]);
$participated_events = $participated_events_stmt->fetchColumn();

$participated_seconds_stmt = $pdo->prepare(<<<SQL
SELECT SUM(TIME_TO_SEC(TIMEDIFF(Shift.to_time, Shift.from_time))) FROM Shift, UserShift
WHERE Shift.id = UserShift.shift_id
  AND UserShift.user_id = ?;
SQL);
$participated_seconds_stmt->execute([$user->id]);
$participated_seconds = $participated_seconds_stmt->fetchColumn();
// to days and hours (render days only if > 0)
$participated_time = "Keine";
if ($participated_seconds != NULL) {
    if (floor($participated_seconds / 86400) > 0) {
        $participated_time = floor($participated_seconds / 86400) . "d " . gmdate("H", $participated_seconds) . "h " . gmdate("i", $participated_seconds) . "m";
    } else {
        $participated_time = gmdate("H", $participated_seconds) . "h " . gmdate("i", $participated_seconds) . "m";
    }
}


ok([
    "own_events" => $own_events,
    "participated_events" => $participated_events,
    "participated_time" => $participated_time
]);