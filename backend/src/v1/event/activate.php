<?php

use Modules\Email\Automailer;
use Modules\Email\View;
use Modules\Event\Event;
use Modules\Event\EventPreset;
use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

$user = init(Permission::DeactivateEvent);

if (!isset($_GET['id'])) {
    error('Bitte alle Felder ausfüllen');
}

$event = Event::byId($_GET['id']);

if ($event === null) {
    error('Event nicht gefunden');
}

$event->disabled = false;
$event->save();
