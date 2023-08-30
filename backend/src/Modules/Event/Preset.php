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
        $db = database();
        $result = $db->prepare('SELECT * FROM Preset WHERE `id` = ?');
        $result->execute([$id]);
        if ($result->rowCount() === 0) {
            return null;
        }
        $row = $result->fetch();
        return new Preset(
            id: $row['id'],
            tech: $row['tech'],
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "tech" => $this->tech,
        ];
    }
}