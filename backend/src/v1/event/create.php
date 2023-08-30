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

$user = init(Permission::Login);

$body = json_decode(file_get_contents('php://input'));

if (
    !isset($body->title) ||
    !isset($body->description) ||
    !isset($body->type_id) ||
    !isset($body->room_id) ||
    !isset($body->needs_consultation) ||
    !isset($body->from) ||
    !isset($body->to) ||
    !isset($body->construction_from) ||
    !isset($body->construction_to) ||
    !isset($body->dismantling_from) ||
    !isset($body->dismantling_to) ||
    !isset($body->presets) ||
    !is_array($body->presets) ||
    !isset($body->captcha) ||
    !isset($body->captcha_id)
) {
    error('Bitte alle Felder ausfüllen');
}

$db = database();

$statement = $db->prepare('SELECT `id` FROM `CompletelyAutomatedPublicTuringTestToTellComputersAndHumansApart` WHERE `answer` = ? AND `timeout` > NOW() AND `id` = ?');
$statement->execute([$body->captcha, $body->captcha_id]);

if ($statement->rowCount() === 0) {
    error('Captcha falsch');
}

try {
    $event = Event::create(
        organizer_id: $user->id,
        type_id: $body->type_id,
        room_id: $body->room_id,
        title: $body->title,
        description: $body->description,
        needs_consultation: $body->needs_consultation,
        from_time: $body->from,
        to_time: $body->to,
        construction_from: $body->construction_from,
        construction_to: $body->construction_to,
        dismantling_from: $body->dismantling_from,
        dismantling_to: $body->dismantling_to
    );

    for ($i = 0; $i < count($body->presets); $i++) {
        $preset = $body->presets[$i];
        EventPreset::create($event->id, $preset);
    }

} catch (Exception $e) {
    error("Eingabe ungültig");
}

$statement = $db->prepare('DELETE FROM `CompletelyAutomatedPublicTuringTestToTellComputersAndHumansApart` WHERE `id` = ?');
$statement->execute([$body->captcha_id]);

(new Automailer(new View("new_event"), function (User $user) use ($event) {
    return [
        "user" => $user,
        "eventname" => $event->title,
    ];
}, "Goe-Tec Automailer: Neue Veranstaltung"))
    ->filterByPermission(Permission::ReceiveAutomailer)
    ->send();

ok($event->toArray());