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
        $db = database();
        $result = $db->prepare('SELECT * FROM Room WHERE `id` = ?');
        $result->execute([$id]);
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