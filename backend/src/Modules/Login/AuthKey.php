<?php

namespace Modules\Login;

use function Modules\Utils\database;

class AuthKey
{
    public int $id;
    public int $user_id;
    public string $key;
    public string $method;
    public bool $once_only;

    public static string $METHOD_EMAIL = 'email';
    public static string $METHOD_REGISTRATION = 'registration';
    public static string $METHOD_PASSWORD_RESET = 'password_reset';

    public function __construct(int $id, int $user_id, string $key, string $method, bool $once_only)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->key = $key;
        $this->method = $method;
        $this->once_only = $once_only;
    }

    public static function fromUser(User $user): array
    {
        $db = database();
        $stmt = $db->prepare('SELECT * FROM AuthKey WHERE user_id = ?');
        $stmt->execute([$user->id]);
        $result = $stmt->fetchAll();
        if (!$result) {
            return [];
        }

        $authKeys = [];
        foreach ($result as $row) {
            $authKeys[] = new AuthKey($row['id'], $row['user_id'], $row['key'], $row['method'], $row['once_only']);
        }

        return $authKeys;
    }
}