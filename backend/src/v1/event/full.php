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

$event = Event::byId($id_int);
if (!$event) {
    error('Event nicht gefunden');
}

if (!$canViewAllEvents && $event->organizer_id !== $user->id) {
    error('Keine Berechtigung');
}

$eventData = $event->toArray();
$eventData['organizer'] = $event->organizer()->toArray();
$eventData['room'] = $event->room()->name;
$eventData['type'] = $event->type()->name;
$eventData['hdr_unsplash_id'] = $event->type()->unsplash_id;
$eventData['shifts'] = map($event->shifts(), fn($shift) => $shift->toArray());
$eventData['presets'] = map(EventPreset::byEventId($event->id), fn($preset) => Preset::byId($preset->preset_id)->toArray());
try {
    $eventData['logs'] = map(Log::byEventId($event->id), fn($log) => $log->toArray());
} catch (Exception $e) {
    $eventData['logs'] = [[
        "id" => 0,
        "event_id" => $event->id,
        "user_id" => 0,
        "user_name" => "System",
        "timestamp" => date('Y-m-d H:i:s'),
        "message" => "Fehler beim Laden der Logs",
        "type" => "change",
    ]];
}

ok($eventData);