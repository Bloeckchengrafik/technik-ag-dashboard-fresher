<?php

namespace Modules\Login;

use Closure;
use Exception;
use Modules\Email\Mailer;
use Modules\Email\View;
use function Modules\Utils\database;

class User
{
    private array $_oldPermissions;
    private array $_oldGroups;

    public function __construct(
        public int    $id,
        public string $firstname,
        public string $lastname,
        public string $email,
        public array  $permissions = [],
        public array  $groups = []
    )
    {
        $this->_oldPermissions = $permissions;
        $this->_oldGroups = $groups;
    }

    public static function all(): array
    {
        $db = database();
        $stmt = $db->prepare('SELECT id FROM User');
        $stmt->execute();
        $results = $stmt->fetchAll();
        $users = [];
        foreach ($results as $result) {
            $users[] = self::fromId($result['id']);
        }
        return $users;
    }

    public function save(): void
    {
        $db = database();
        $stmt = $db->prepare('UPDATE User SET firstname = ?, lastname = ?, email = ? WHERE id = ?');
        $stmt->execute([$this->firstname, $this->lastname, $this->email, $this->id]);

        $this->updatePermissions();
        $this->updateGroups();
    }

    /**
     * @throws Exception
     */
    public function sendConfirmationEmail(): void
    {
        $keys = AuthKey::fromUser($this);
        foreach ($keys as $authKey) {
            if ($authKey->method === AuthKey::$METHOD_REGISTRATION) {
                $authKey->delete();
            }
        }

        $random = random_bytes(3);
        $key = bin2hex($random);
        AuthKey::create($this, $key, AuthKey::$METHOD_REGISTRATION, true);

        $view = new View("register");
        $html = $view->render([
            'user' => $this,
            'key' => $key,
        ]);
        Mailer::send($this->email, "GOETEC Email-Verifikation", $html);
    }

    public static function fromToken(string $token): ?User
    {
        return Jwt\decode($token);
    }

    public static function create($firstname, $lastname, $email): User
    {
        $db = database();
        $stmt = $db->prepare('INSERT INTO User (firstname, lastname, email) VALUES (?, ?, ?)');
        $stmt->execute([$firstname, $lastname, $email]);
        $id = $db->lastInsertId();
        return new User($id, $firstname, $lastname, $email);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'permission' => $this->getAllPermissions(),
            'groups' => $this->groups,
        ];
    }

    public function createJwt(): string
    {
        return Jwt\encode($this);
    }

    public static function fromEmail(string $email): ?User
    {
        $db = database();
        $stmt = $db->prepare('SELECT id FROM User WHERE email = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }

        return self::fromId($result[0]);
    }

    public static function fromId(int $id): ?User
    {
        $db = database();
        $stmt = $db->prepare('SELECT * FROM User WHERE id = ?');
        $stmt->execute([$id]);
        $userResult = $stmt->fetch();
        if (!$userResult) {
            return null;
        }

        $perms = [];
        $stmt = $db->prepare('SELECT Permission.name FROM UserPermission, Permission WHERE user_id = ? AND permission_id = Permission.id');
        $stmt->execute([$userResult['id']]);
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $perms[] = $result['name'];
        }

        $groups = [];
        $stmt = $db->prepare('SELECT `Group`.name FROM UserGroup, `Group` WHERE user_id = ? AND group_id = `Group`.id');
        $stmt->execute([$userResult['id']]);
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $groups[] = $result['name'];
        }

        return new User($userResult['id'], $userResult['firstname'], $userResult['lastname'], $userResult['email'], $perms, $groups);
    }

    public function useKey(Closure $keyValidate, string $type): bool
    {
        $keys = AuthKey::fromUser($this);
        foreach ($keys as $authKey) {
            if ($keyValidate($authKey->key) && $authKey->method === $type) {
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

    public function isStudent(): bool
    {
        return StudentInfo::fromUser($this) !== null;
    }

    public function getStudentInfo(): ?StudentInfo
    {
        return StudentInfo::fromUser($this);
    }

    private function updatePermissions(): void
    {
        $added = array_diff($this->permissions, $this->_oldPermissions);
        $removed = array_diff($this->_oldPermissions, $this->permissions);

        foreach ($added as $permission) {
            $db = database();
            $stmt = $db->prepare('INSERT INTO UserPermission (user_id, permission_id) VALUES (?, (SELECT id FROM Permission WHERE name = ?))');
            $stmt->execute([$this->id, $permission]);
        }

        foreach ($removed as $permission) {
            $db = database();
            $stmt = $db->prepare('DELETE FROM UserPermission WHERE user_id = ? AND permission_id = (SELECT id FROM Permission WHERE name = ?)');
            $stmt->execute([$this->id, $permission]);
        }
    }

    private function updateGroups(): void
    {
        $added = array_diff($this->groups, $this->_oldGroups);
        $removed = array_diff($this->_oldGroups, $this->groups);

        foreach ($added as $group) {
            $db = database();
            $stmt = $db->prepare('INSERT INTO UserGroup (user_id, group_id) VALUES (?, (SELECT id FROM `Group` WHERE name = ?))');
            $stmt->execute([$this->id, $group]);
        }

        foreach ($removed as $group) {
            $db = database();
            $stmt = $db->prepare('DELETE FROM UserGroup WHERE user_id = ? AND group_id = (SELECT id FROM `Group` WHERE name = ?)');
            $stmt->execute([$this->id, $group]);
        }
    }

    public function getAllPermissions(): array
    {
        $db = database();
        $stmt = $db->prepare(<<<SQL
SELECT Permission.name 
FROM UserPermission, Permission
WHERE (UserPermission.user_id = ? AND UserPermission.permission_id = Permission.id)
SQL);

        $stmtGroups = $db->prepare(<<<SQL
SELECT Permission.name 
FROM Permission, GroupPermission, UserGroup
WHERE (UserGroup.user_id = ? AND UserGroup.group_id = GroupPermission.group_id AND GroupPermission.permission_id = Permission.id)
SQL);

        $stmt->execute([$this->id]);
        $stmtGroups->execute([$this->id]);
        $results = $stmt->fetchAll();
        $results = array_merge($results, $stmtGroups->fetchAll());
        $perms = [];
        foreach ($results as $result) {
            $perms[] = $result['name'];
        }
        return $perms;
    }

    public function hasPermission(Permission $requiredAuth): bool
    {
        return in_array($requiredAuth->value, $this->getAllPermissions());
    }

    /**
     * @throws Exception
     */
    public function sendPasswordResetEmail(): void
    {
        $rndm = random_bytes(3);
        $key = bin2hex($rndm);

        AuthKey::create($this, $key, AuthKey::$METHOD_PASSWORD_RESET, true);

        $view = new View('password_reset');
        $html = $view->render([
            'key' => $key,
            'user' => $this,
        ]);

        Mailer::send($this->email, 'GOETEC Passwortreset', $html);
    }
}