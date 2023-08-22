<?php
namespace Modules\Utils\Json;

use JetBrains\PhpStorm\NoReturn;

/// This function just returns the specified array as json and exits
#[NoReturn] function ok(array $json): void
{
    header('Content-Type: application/json');
    echo json_encode($json);
    exit;
}

#[NoReturn] function error($message, $code = 400): void
{
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}