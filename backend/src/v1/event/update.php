<?php

use Modules\Event\Event;
use Modules\Event\EventPreset;
use Modules\Event\Log;
use Modules\Event\Preset;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Api\map;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

$user = init(Permission::Login);

if (!isset($_GET['id'])) {
    error('Bitte alle Felder ausfüllen');
}

$db = database();
$canViewAllEvents = $user->hasPermission(Permission::ViewAllEvents);

$id_int = intval($_GET['id']);
$body = json_decode(file_get_contents('php://input'));

$event = Event::byId($id_int);
if (!$event) {
    error('Event nicht gefunden');
}

if (!$canViewAllEvents && $event->organizer_id !== $user->id) {
    error('Keine Berechtigung');
}


if (isset($body->title)) $event->title = $body->title;
if (isset($body->description)) $event->description = $body->description;

$event->save();