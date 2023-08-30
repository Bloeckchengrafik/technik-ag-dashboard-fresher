<?php

namespace Modules\Event;

use Exception;

enum LogType: string {
    case Chat = "chat";
    case Change = "Change";


    /**
     * @throws Exception
     */
    public static function fromString(string $type): LogType {
        return match ($type) {
            "chat" => LogType::Chat,
            "change" => LogType::Change,
            default => throw new Exception("Invalid LogType: $type")
        };
    }
}