<?php

namespace Modules\Event;

use function Modules\Utils\database;

class EventType
{
    public function __construct(
        public int    $id,
        public string $name,
    )
    {
    }

    public static function byId(int $id): ?EventType
    {
        $result = database()->query('SELECT * FROM EventType WHERE id = ?', [$id]);
        if ($result->rowCount() === 0) {
            return null;
        }
        $row = $result->fetch();
        return new EventType(
            id: $row['id'],
            name: $row['name'],
        );
    }
}