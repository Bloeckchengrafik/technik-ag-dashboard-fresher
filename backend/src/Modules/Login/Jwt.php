<?php
namespace Modules\Login\Jwt;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Modules\Login\User;

function encode(User $user): string
{
    $payload = $GLOBALS['config']['jwt']['payloadbase'];
    $payload['user'] = $user->toArray();
    $payload['iat'] = time();
    $payload['exp'] = time() + $GLOBALS['config']['jwt']['expiration'];
    return JWT::encode($payload, $GLOBALS['config']['jwt']['privatekey'], "RS256");
}

function decode($jwt): ?User
{
    try {
        $decoded = JWT::decode($jwt, new Key($GLOBALS['config']['jwt']['publickey'], "RS256"));
        return User::fromId($decoded->user->id);
    } catch (Exception $e) {
        // Log error in debug log
        $f = fopen('php://stderr', 'w');
        fwrite($f, $e->getMessage());
        fclose($f);

        return null;
    }
}