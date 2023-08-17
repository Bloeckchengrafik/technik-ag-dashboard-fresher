<?php
namespace Modules\Login\Jwt;

use Firebase\JWT\JWT;
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
        $decoded = JWT::decode($jwt, $GLOBALS['config']['jwt']['publickey']);
        return new User(
            $decoded->user->id,
            $decoded->user->firstname,
            $decoded->user->lastname,
            $decoded->user->email,
            "***",
            $decoded->user->permission
        );
    } catch (\Exception $e) {
        return null;
    }
}