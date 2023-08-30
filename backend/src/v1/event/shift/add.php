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
    !isset($body->event_id) ||
    !isset($body->name) ||
    !isset($body->needed) ||
    !isset($body->from_time) ||
    !isset($body->to_time)
) {
    error('Bitte alle Felder ausfüllen');
}

$shift = Shift::create(
    intval($body->event_id),
    $body->name,
    intval($body->needed),
    $body->from_time,
    $body->to_time,
);

Log::log(intval($body->event_id), $user->id, LogType::Change, "Schicht {$shift->name} für {$shift->needed} Personen von {$shift->from_time} bis {$shift->to_time} Uhr erstellt");

ok([]);