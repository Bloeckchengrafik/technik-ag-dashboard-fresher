<?php

use Modules\Event\Event;
use Modules\Event\Log;
use Modules\Event\LogType;
use Modules\Login\Permission;
use function Modules\Utils\Api\init;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once '../../../Modules/Autoload.php';

$user = init(Permission::Login);

$body = json_decode(file_get_contents('php://input'));
if (
    !isset($body->event_id) ||
    !isset($body->chat_message)
) {
    error('Bitte alle Felder ausfüllen');
}

$event = Event::byId($body->event_id);
if ($event === null) {
    error('Event nicht gefunden');
}

Log::log($event->id, $user->id, LogType::Chat, $body->chat_message);
ok([]);