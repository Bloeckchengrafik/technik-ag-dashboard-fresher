<?php

namespace Modules\Event;

use function Modules\Utils\database;

class Room
{
    public function __construct(
        public int    $id,
        public string $name,
    )
    {
    }

    public static function byId(int $id): ?Room
    {
        $result = database()->query('SELECT * FROM Room WHERE id = ?', [$id]);
        if ($result->rowCount() === 0) {
            return null;
        }
        $row = $result->fetch();
        return new Room(
            id: $row['id'],
            name: $row['name'],
        );
    }
}