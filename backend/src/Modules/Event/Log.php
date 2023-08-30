<?php

namespace Modules\Event;

use Exception;
use Modules\Email\Automailer;
use Modules\Email\View;
use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\database;

class Log
{
    private User $user;
    public function __construct(
        public int    $id,
        public int    $event_id,
        public int    $user_id,
        public string $timestamp,
        public string $message,
        public LogType $type,
    )
    {
        $this->user = User::fromId($user_id);
    }

    public static function log(int $event_id, int $user_id, LogType $type, string $message): void
    {
        $db = database();
        $stmt = $db->prepare("INSERT INTO EventLog (event_id, user_id, timestamp, message, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindValue(1, $event_id);
        $stmt->bindValue(2, $user_id);
        $stmt->bindValue(3, date('Y-m-d H:i:s'));
        $stmt->bindValue(4, $message);
        $stmt->bindValue(5, $type->value);
        $stmt->execute();

        $event = Event::byId($event_id);
        $sender = User::fromId($user_id);

        (new Automailer(new View("message_in_event"), function (User $user) use ($event, $sender, $message) {
            return [
                "user" => $user,
                "eventname" => $event->title,
                "msg" => $message,
                "sending_user" => $sender->firstname . ' ' . $sender->lastname,
            ];
        }, "Goe-Tec Automailer: Neue Nachricht in {$event->title}"))
            ->filterByPermission(Permission::ReceiveAutomailer)
            ->send();
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "event_id" => $this->event_id,
            "user_id" => $this->user_id,
            "user_name" => $this->user->firstname . ' ' . $this->user->lastname,
            "timestamp" => $this->timestamp,
            "message" => $this->message,
            "type" => $this->type->value,
        ];
    }

    /**
     * @throws Exception
     */
    public static function byEventId(int $event_id): array
    {
        $db = database();
        $stmt = $db->prepare("SELECT * FROM EventLog WHERE event_id = ? ORDER BY timestamp DESC");
        $stmt->bindValue(1, $event_id);
        $stmt->execute();
        $logs = [];
        while ($row = $stmt->fetch()) {
            $logs[] = new Log(
                $row['id'],
                $row['event_id'],
                $row['user_id'],
                $row['timestamp'],
                $row['message'],
                LogType::fromString($row['type']),
            );
        }
        return $logs;
    }
}