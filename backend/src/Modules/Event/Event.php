<?php

namespace Modules\Event;

use Modules\Login\User;
use function Modules\Utils\database;

class Event
{
    public function __construct(
        public int    $id,
        public int    $organizer_id,
        public int    $type_id,
        public int    $room_id,
        public string $title,
        public string $description,
        public bool   $needs_consultation,
        public string $from_time,
        public string $to_time,
        public string $construction_from,
        public string $construction_to,
        public string $dismantling_from,
        public string $dismantling_to,
        public bool   $disabled,
    )
    {
    }

    public function organizer(): User
    {
        return User::fromId($this->organizer_id);
    }

    public function type(): EventType
    {
        return EventType::byId($this->type_id);
    }

    public function room(): Room
    {
        return Room::byId($this->room_id);
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "organizer_id" => $this->organizer_id,
            "type_id" => $this->type_id,
            "room_id" => $this->room_id,
            "title" => $this->title,
            "description" => $this->description,
            "needs_consultation" => $this->needs_consultation,
            "from_time" => $this->from_time,
            "to_time" => $this->to_time,
            "construction_from" => $this->construction_from,
            "construction_to" => $this->construction_to,
            "dismantling_from" => $this->dismantling_from,
            "dismantling_to" => $this->dismantling_to,
            "disabled" => $this->disabled,
        ];
    }

    public function update(
        int    $organizer_id,
        int    $type_id,
        int    $room_id,
        string $title,
        string $description,
        bool   $needs_consultation,
        string $from_time,
        string $to_time,
        string $construction_from,
        string $construction_to,
        string $dismantling_from,
        string $dismantling_to
    ): void
    {
        $db = database();
        $stmt = $db->prepare(<<<SQL
UPDATE Event SET
    organizer_id = ?,
    type_id = ?,
    room_id = ?,
    title = ?,
    description = ?,
    needs_consultation = ?,
    from_time = ?,
    to_time = ?,
    construction_from = ?,
    construction_to = ?,
    dismantling_from = ?,
    dismantling_to = ?,
    disabled = ?
WHERE id = ?
SQL
        );
        $stmt->execute([
            $organizer_id,
            $type_id,
            $room_id,
            $title,
            $description,
            $needs_consultation,
            $from_time,
            $to_time,
            $construction_from,
            $construction_to,
            $dismantling_from,
            $dismantling_to,
            $this->disabled,
            $this->id,
        ]);
    }


    public static function create(
        int    $organizer_id,
        int    $type_id,
        int    $room_id,
        string $title,
        string $description,
        bool   $needs_consultation,
        string $from_time,
        string $to_time,
        string $construction_from,
        string $construction_to,
        string $dismantling_from,
        string $dismantling_to
    ): Event
    {
        $db = database();
        $stmt = $db->prepare(<<<SQL
INSERT INTO Event 
    (
     organizer_id, 
     type_id, 
     room_id, 
     title, 
     description, 
     needs_consultation, 
     from_time, 
     to_time, 
     construction_from, 
     construction_to, 
     dismantling_from, 
     dismantling_to
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL
        );
        $stmt->execute([
            $organizer_id,
            $type_id,
            $room_id,
            $title,
            $description,
            $needs_consultation ? 1 : 0,
            $from_time,
            $to_time,
            $construction_from,
            $construction_to,
            $dismantling_from,
            $dismantling_to,
        ]);

        return new Event(
            id: $db->lastInsertId(),
            organizer_id: $organizer_id,
            type_id: $type_id,
            room_id: $room_id,
            title: $title,
            description: $description,
            needs_consultation: $needs_consultation,
            from_time: $from_time,
            to_time: $to_time,
            construction_from: $construction_from,
            construction_to: $construction_to,
            dismantling_from: $dismantling_from,
            dismantling_to: $dismantling_to,
            disabled: false,
        );
    }

    private static function fromResult(array $result): Event
    {
        return new Event(
            id: $result['id'],
            organizer_id: $result['organizer_id'],
            type_id: $result['type_id'],
            room_id: $result['room_id'],
            title: $result['title'],
            description: $result['description'],
            needs_consultation: $result['needs_consultation'],
            from_time: $result['from_time'],
            to_time: $result['to_time'],
            construction_from: $result['construction_from'],
            construction_to: $result['construction_to'],
            dismantling_from: $result['dismantling_from'],
            dismantling_to: $result['dismantling_to'],
            disabled: $result['disabled'],
        );
    }

    public static function byId(int $id): ?Event
    {
        $database = database();
        $stmt = $database->prepare('SELECT * FROM Event WHERE id = ?');
        $stmt->bindValue(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }
        $row = $stmt->fetch();
        return self::fromResult($row);
    }

    public static function all(): array
    {
        $result = database()->query('SELECT * FROM Event');
        $events = [];
        foreach ($result->fetchAll() as $row) {
            $events[] = self::fromResult($row);
        }
        return $events;
    }

    public static function byOrganizerId(int $organizer_id): array
    {
        $database = database();
        $stmt = $database->prepare('SELECT * FROM Event WHERE organizer_id = ?');
        $stmt->bindValue(1, $organizer_id);
        $stmt->execute();

        $events = [];
        foreach ($stmt->fetchAll() as $row) {
            $events[] = self::fromResult($row);
        }
        return $events;
    }

    public function shifts(): array
    {
        return Shift::byEventId($this->id);
    }

    public function save(): void
    {
        $db = database();
        $stmt = $db->prepare(<<<SQL
UPDATE Event SET
    organizer_id = ?,
    type_id = ?,
    room_id = ?,
    title = ?,
    description = ?,
    needs_consultation = ?,
    from_time = ?,
    to_time = ?,
    construction_from = ?,
    construction_to = ?,
    dismantling_from = ?,
    dismantling_to = ?,
    disabled = ?
WHERE id = ?
SQL
        );
        $stmt->execute([
            $this->organizer_id,
            $this->type_id,
            $this->room_id,
            $this->title,
            $this->description,
            $this->needs_consultation ? 1 : 0,
            $this->from_time,
            $this->to_time,
            $this->construction_from,
            $this->construction_to,
            $this->dismantling_from,
            $this->dismantling_to,
            $this->disabled ? 1 : 0,
            $this->id,
        ]);
    }
}