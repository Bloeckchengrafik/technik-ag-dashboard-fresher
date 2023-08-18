<?php
namespace Modules\Utils\Api;

use Modules\Login\User;
use function Modules\Utils\Json\error;

function init($requiredAuth = 0): ?User
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
    header('Access-Control-Allow-Methods:  POST, PUT, GET');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit();
    }

    if ($requiredAuth) {
        // Get bearer token from header
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            error('Authorization header not set', 401);
        }

        $token = $headers['Authorization'];
        if (!str_starts_with($token, 'Bearer ')) {
            error('Authorization header must start with "Bearer "', 401);
        }

        $token = substr($token, 7);

        // Verify token
        $user = User::fromToken($token);

        if (!$user) {
            error('Invalid token', 401);
        }

        return $user;
    }

    return null;
}