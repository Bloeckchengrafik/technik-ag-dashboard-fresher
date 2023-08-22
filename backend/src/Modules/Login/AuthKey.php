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

    public function delete(): void
    {
        $db = database();
        $stmt = $db->prepare('DELETE FROM AuthKey WHERE id = ?');
        $stmt->execute([$this->id]);
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
            $authKeys[] = new AuthKey($row['id'], $row['user_id'], $row['key'], $row['method'], $row['once_only'] === 1);
        }

        return $authKeys;
    }

    public static function create(User $user, string $theKey, string $method, bool $onceOnly): AuthKey
    {
        $db = database();
        $stmt = $db->prepare('INSERT INTO AuthKey (user_id, `key`, method, once_only) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user->id, $theKey, $method, $onceOnly ? 1 : 0]);
        $id = $db->lastInsertId();
        return new AuthKey($id, $user->id, $theKey, $method, $onceOnly);
    }
}