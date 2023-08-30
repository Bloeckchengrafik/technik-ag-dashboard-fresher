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

if ($_GET['id'] === null) {
    error('Bitte alle Felder ausfüllen');
}

$shift = Shift::byId(intval($_GET['id']));
if ($shift === null) {
    error('Schicht nicht gefunden');
}

$shift->delete();
Log::log($shift->event_id, $user->id, LogType::Change, "Schicht {$shift->name} gelöscht");

ok([]);