<?php

namespace Modules\Event;

use function Modules\Utils\database;

class Preset
{
    public function __construct(
        public int    $id,
        public string $tech,
    )
    {
    }

    public static function byId(int $id): ?Preset
    {
        $result = database()->query('SELECT * FROM Preset WHERE id = ?', [$id]);
        if ($result->rowCount() === 0) {
            return null;
        }
        $row = $result->fetch();
        return new Preset(
            id: $row['id'],
            tech: $row['tech'],
        );
    }
}