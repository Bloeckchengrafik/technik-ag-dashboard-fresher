<?php
namespace Modules\Login;

use Exception;
use Modules\Email\Mailer;
use Modules\Email\View;
use function Modules\Utils\database;

class User {
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public int $permission;

    public static int $PERMISSION_WATCHER = 1;
    public static int $PERMISSION_TECHNICIAN = 2;
    public static int $PERMISSION_MANAGER = 3;
    public static int $PERMISSION_ADMIN = 4;

    public function __construct(int $id, string $firstname, string $lastname, string $email, int $permission)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->permission = $permission;
    }

    public function save(): void
    {
        $db = database();
        $stmt = $db->prepare('UPDATE User SET firstname = ?, lastname = ?, email = ?, permission = ? WHERE id = ?');
        $stmt->execute([$this->firstname, $this->lastname, $this->email, $this->permission, $this->id]);
    }

    /**
     * @throws Exception
     */
    public function sendConfirmationEmail(): void {
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
        $stmt = $db->prepare('INSERT INTO User (firstname, lastname, email, permission) VALUES (?, ?, ?, 1)');
        $stmt->execute([$firstname, $lastname, $email]);
        $id = $db->lastInsertId();
        return new User($id, $firstname, $lastname, $email, 0);
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

    public function isStudent(): bool {
        return StudentInfo::fromUser($this) !== null;
    }

    public function getStudentInfo(): ?StudentInfo {
        return StudentInfo::fromUser($this);
    }
}