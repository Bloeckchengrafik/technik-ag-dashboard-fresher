<?php
namespace Modules\Utils\Json;

/// This function just returns the specified array as json and exits
function ok(array $json)
{
    header('Content-Type: application/json');
    echo json_encode($json);
    exit;
}

function error($message, $code = 400)
{
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}