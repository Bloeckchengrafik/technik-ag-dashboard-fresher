<?php

use Modules\Event\Event;
use Modules\Event\EventPreset;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../Modules/Autoload.php';

$user = init(Permission::LOGIN);

$body = json_decode(file_get_contents('php://input'));

if (
    !isset($body->title) ||
    !isset($body->description) ||
    !isset($body->type_id) ||
    !isset($body->room_id) ||
    !isset($body->location) ||
    !isset($body->needs_consultation) ||
    !isset($body->from) ||
    !isset($body->to) ||
    !isset($body->construction_from) ||
    !isset($body->construction_to) ||
    !isset($body->dismantling_from) ||
    !isset($body->dismantling_to) ||
    !isset($body->presets)
) {
    error('Bitte alle Felder ausfüllen');
}

$event = Event::create(
    $user->id,
    $body->type_id,
    $body->room_id,
    $body->title,
    $body->description,
    $body->needs_consultation,
    $body->from,
    $body->to,
    $body->construction_from,
    $body->construction_to,
    $body->dismantling_from,
    $body->dismantling_to
);

for ($i = 0; $i < count($body->presets); $i++) {
    $preset = $body->presets[$i];
    EventPreset::create($event->id, $preset->preset_id);
}

ok($event->toArray());