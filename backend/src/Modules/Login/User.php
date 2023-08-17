<?php
namespace Modules\Login;

use function Modules\Utils\database;

class User {
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public int $permission;

    public function __construct(int $id, string $firstname, string $lastname, string $email, int $permission)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->permission = $permission;
    }

    public static function fromToken(string $token): ?User
    {
        return Jwt\decode($token);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'permission' => $this->permission,
        ];
    }

    public function createJwt(): string
    {
        return Jwt\encode($this);
    }

    public static function fromEmail(string $email): ?User
    {
        $db = database();
        $stmt = $db->prepare('SELECT * FROM User WHERE email = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }

        return new User($result['id'], $result['firstname'], $result['lastname'], $result['email'], $result['permission']);
    }

    public static function fromId(string $email): ?User
    {
        $db = database();
        $stmt = $db->prepare('SELECT * FROM User WHERE id = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }

        return new User($result['id'], $result['firstname'], $result['lastname'], $result['email'], $result['permission']);
    }

    public function useKey(string $key, string $type): bool {
        $keys = AuthKey::fromUser($this);
        foreach ($keys as $authKey) {
            if ($authKey->key === $key && $authKey->method === $type) {
                if ($authKey->once_only) {
                    $db = database();
                    $stmt = $db->prepare('DELETE FROM AuthKey WHERE id = ?');
                    $stmt->execute([$authKey->id]);
                }
                return true;
            }
        }
        return false;
    }
}