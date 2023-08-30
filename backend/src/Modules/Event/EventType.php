<?php

namespace Modules\Event;

use function Modules\Utils\database;

class EventType
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $unsplash_id
    )
    {
    }

    public static function byId(int $id): ?EventType
    {
        $db = database();
        $result = $db->prepare('SELECT * FROM EventType WHERE `id` = ?');
        $result->execute([$id]);
        if ($result->rowCount() === 0) {
            return null;
        }
        $row = $result->fetch();
        return new EventType(
            id: $row['id'],
            name: $row['name'],
            unsplash_id: $row['unsplash_id'],
        );
    }
}