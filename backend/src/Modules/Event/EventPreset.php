<?php

namespace Modules\Event;

use function Modules\Utils\database;

class EventPreset
{
    public function __construct(
        public int $event_id,
        public int $preset_id,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "event_id" => $this->event_id,
            "preset_id" => $this->preset_id,
        ];
    }

    public function delete(): void
    {
        database()->query('DELETE FROM EventPreset WHERE event_id = ? AND preset_id = ?', [$this->event_id, $this->preset_id]);
    }

    public static function create(int $event_id, int $preset_id): EventPreset
    {
        database()->query('INSERT INTO EventPreset (event_id, preset_id) VALUES (?, ?)', [$event_id, $preset_id]);
        return new EventPreset(
            event_id: $event_id,
            preset_id: $preset_id,
        );
    }

    public static function byEventId(int $event_id): array
    {
        $result = database()->query('SELECT * FROM EventPreset WHERE event_id = ?', [$event_id]);
        $presets = [];
        while ($row = $result->fetch()) {
            $presets[] = new EventPreset(
                event_id: $row['event_id'],
                preset_id: $row['preset_id'],
            );
        }
        return $presets;
    }
}