<?php

use Modules\Event\Shift;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../../Modules/Autoload.php';

$user = init(Permission::JoinShifts);

if (!isset($_GET['id'])) {
    error('Bitte alle Felder ausfüllen');
}

$shift = Shift::byId($_GET['id']);
if ($shift === null) {
    error('Schicht nicht gefunden');
}

$shift->leave($user->id);
ok([]);