<?php

namespace Modules\Event;

use Modules\Login\User;
use function Modules\Utils\Api\map;
use function Modules\Utils\database;

class Shift
{
    function __construct(
        public int    $id,
        public int    $event_id,
        public string $name,
        public int    $needed,
        public string $from_time,
        public string $to_time,
    )
    {
    }

    public function userIds(): array
    {
        $db = database();
        $stmt = $db->prepare("SELECT user_id FROM UserShift WHERE shift_id = ?");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $userIds = [];
        while ($row = $stmt->fetch()) {
            $userIds[] = $row['user_id'];
        }
        return $userIds;
    }

    public function users(): array
    {
        $db = database();
        $stmt = $db->prepare("SELECT user_id FROM UserShift WHERE shift_id = ?");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = User::fromId($row['user_id']);
        }
        return $users;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "event_id" => $this->event_id,
            "name" => $this->name,
            "needed" => $this->needed,
            "from_time" => $this->from_time,
            "to_time" => $this->to_time,
            "users" => map($this->users(), fn($user) => $user->toArray()),
        ];
    }

    public static function byId(int $id): ?Shift
    {
        $db = database();
        $stmt = $db->prepare("SELECT * FROM Shift WHERE id = ?");
        $stmt->bindValue(1, $id);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return null;
        }
        $row = $stmt->fetch();
        return new Shift(
            $row['id'],
            $row['event_id'],
            $row['name'],
            $row['needed'],
            $row['from_time'],
            $row['to_time'],
        );
    }

    public static function byEventId(int $event_id): array
    {
        $db = database();
        $stmt = $db->prepare("SELECT * FROM Shift WHERE event_id = ?");
        $stmt->bindValue(1, $event_id);
        $stmt->execute();
        $shifts = [];
        while ($row = $stmt->fetch()) {
            $shifts[] = new Shift(
                $row['id'],
                $row['event_id'],
                $row['name'],
                $row['needed'],
                $row['from_time'],
                $row['to_time'],
            );
        }
        return $shifts;
    }

    public static function create(int $event_id, string $name, int $needed, string $from_time, string $to_time): Shift
    {
        $db = database();
        $stmt = $db->prepare("INSERT INTO Shift (event_id, name, needed, from_time, to_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindValue(1, $event_id);
        $stmt->bindValue(2, $name);
        $stmt->bindValue(3, $needed);
        $stmt->bindValue(4, $from_time);
        $stmt->bindValue(5, $to_time);
        $stmt->execute();
        return new Shift(
            $db->lastInsertId(),
            $event_id,
            $name,
            $needed,
            $from_time,
            $to_time,
        );
    }

    public function join(int $id): void
    {
        // idempotent function for joining a shift
        $db = database();
        $stmt = $db->prepare("INSERT IGNORE INTO UserShift (user_id, shift_id) VALUES (?, ?)");
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $this->id);
        $stmt->execute();
    }

    public function leave(int $id): void
    {
        // idempotent function for leaving a shift
        $db = database();
        $stmt = $db->prepare("DELETE FROM UserShift WHERE user_id = ? AND shift_id = ?");
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $this->id);
        $stmt->execute();
    }

    public function save(): void
    {
        $db = database();
        $stmt = $db->prepare("UPDATE Shift SET event_id = ?, name = ?, needed = ?, from_time = ?, to_time = ? WHERE id = ?");
        $stmt->bindValue(1, $this->event_id);
        $stmt->bindValue(2, $this->name);
        $stmt->bindValue(3, $this->needed);
        $stmt->bindValue(4, $this->from_time);
        $stmt->bindValue(5, $this->to_time);
        $stmt->bindValue(6, $this->id);
        $stmt->execute();
    }

    public function delete(): void
    {
        $db = database();
        $stmt = $db->prepare("DELETE FROM UserShift WHERE shift_id = ?");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $stmt = $db->prepare("DELETE FROM Shift WHERE id = ?");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
    }
}