<?php

use Modules\Event\Log;
use Modules\Event\LogType;
use Modules\Event\Shift;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../../Modules/Autoload.php';

$user = init(Permission::EditShifts);

$body = json_decode(file_get_contents('php://input'));
if (
    !isset($body->shift_id) ||
    !isset($body->name) ||
    !isset($body->needed) ||
    !isset($body->from_time) ||
    !isset($body->to_time)
) {
    error('Bitte alle Felder ausfüllen');
}

$shift = Shift::byId($body->shift_id);
if ($shift === null) {
    error('Schicht nicht gefunden');
}

$shift->name = $body->name;
$shift->needed = intval($body->needed);
$shift->from_time = $body->from_time;
$shift->to_time = $body->to_time;
$shift->save();

Log::log($shift->event_id, $user->id, LogType::Change, "Schicht {$shift->name} für {$shift->needed} Personen von {$shift->from_time} bis {$shift->to_time} Uhr bearbeitet");

ok([]);