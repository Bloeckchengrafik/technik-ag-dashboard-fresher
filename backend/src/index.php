<?php
include_once 'Modules/Autoload.php';

use Modules\Utils\Json;

$goetheIsAvailable = gethostbyname('goethe-bensheim.de') !== 'goethe-bensheim.de';

Json\ok([
    'message' => 'We\'re up and running!',
    'time' => time(),
    'supportedApiVersions' => ['v1'],
    'currentApiVersion' => 'v1',
    'status' => 'ok',
    'systems' => [
        'php' => true,
        'mysql' => true,
        'ggb' => $goetheIsAvailable,
    ],
]);
