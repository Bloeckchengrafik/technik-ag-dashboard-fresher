<?php

namespace Modules\Login;

use PDO;
use function Modules\Utils\Api\map;
use function Modules\Utils\database;

class Group
{
    private array $oldPermissions;
    private array $oldUsers;

    public function __construct(
        public int    $id,
        public string $name,
        public array  $permissions,
        public array  $users,
    )
    {
        $this->oldPermissions = $permissions;
        $this->oldUsers = $users;
    }

    public function hasPermission(Permission $permission): bool
    {
        return in_array($permission->value, $this->permissions);
    }

    public function hasUser(User $user): bool
    {
        return in_array($user->id, $this->users);
    }

    public function addPermission(Permission $permission): void
    {
        $this->permissions[] = $permission->value;
    }

    public function addUser(User $user): void
    {
        $this->users[] = $user->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->permissions,
            'users' => $this->users,
        ];
    }

    public function resolveToArray(): array {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT `name` FROM `Permission` WHERE `id` IN (SELECT `permission_id` FROM `GroupPermission` WHERE `group_id` = :group_id)');
        $stmt->execute([':group_id' => $this->id]);
        $permissions = map($stmt->fetchAll(PDO::FETCH_COLUMN), fn($permission) => Permission::from($permission)->value);

        $stmt = $pdo->prepare('SELECT User.id, User.firstname, User.lastname, User.email FROM `User` INNER JOIN `UserGroup` ON User.id = UserGroup.user_id WHERE UserGroup.group_id = :group_id');
        $stmt->execute([':group_id' => $this->id]);
        $users = map($stmt->fetchAll(PDO::FETCH_ASSOC), fn($user) => (new User($user['id'], $user['firstname'], $user['lastname'], $user['email']))->toArray());

        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $permissions,
            'users' => $users,
        ];
    }

    public function removePermission(Permission $permission): void
    {
        $this->permissions = array_diff($this->permissions, [$permission->value]);
    }

    public function removeUser(User $user): void
    {
        $this->users = array_diff($this->users, [$user->id]);
    }

    public function save(): void
    {
        $pdo = database();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('UPDATE `Group` SET `name` = :name WHERE `id` = :id');
        $stmt->execute([
            ':id'   => $this->id,
            ':name' => $this->name,
        ]);

        $this->update_permissions($pdo);
        $this->update_users($pdo);

        $pdo->commit();
    }

    private function update_permissions(PDO $db): void {
        $diff_add = array_diff($this->permissions, $this->oldPermissions);
        $diff_remove = array_diff($this->oldPermissions, $this->permissions);

        $stmt = $db->prepare('INSERT IGNORE INTO `GroupPermission` (`group_id`, `permission_id`) VALUES (:group_id, (SELECT `id` FROM `Permission` WHERE `name` = :permission_id))');
        foreach ($diff_add as $permission) {
            $stmt->execute([
                ':group_id'     => $this->id,
                ':permission_id' => $permission,
            ]);
        }

        $stmt = $db->prepare('DELETE FROM `GroupPermission` WHERE `group_id` = :group_id AND `permission_id` = (SELECT `id` FROM `Permission` WHERE `name` = :permission_id)');
        foreach ($diff_remove as $permission) {
            $stmt->execute([
                ':group_id'     => $this->id,
                ':permission_id' => $permission,
            ]);
        }

        $this->oldPermissions = $this->permissions;
    }

    private function update_users(PDO $db): void {
        $diff_add = array_diff($this->users, $this->oldUsers);
        $diff_remove = array_diff($this->oldUsers, $this->users);

        $stmt = $db->prepare('INSERT IGNORE INTO `UserGroup` (`group_id`, `user_id`) VALUES (:group_id, :user_id)');
        foreach ($diff_add as $user) {
            $stmt->execute([
                ':group_id' => $this->id,
                ':user_id'  => $user,
            ]);
        }

        $stmt = $db->prepare('DELETE FROM `UserGroup` WHERE `group_id` = :group_id AND `user_id` = :user_id');
        foreach ($diff_remove as $user) {
            $stmt->execute([
                ':group_id' => $this->id,
                ':user_id'  => $user,
            ]);
        }

        $this->oldUsers = $this->users;
    }

    public function initialResolve(): void {
        // Fill in permissions and users
        $pdo = database();
        $stmt = $pdo->prepare('SELECT `name` FROM `Permission` WHERE `id` IN (SELECT `permission_id` FROM `GroupPermission` WHERE `group_id` = :group_id)');
        $stmt->execute([':group_id' => $this->id]);
        $this->permissions = map($stmt->fetchAll(PDO::FETCH_COLUMN), fn($permission) => Permission::from($permission)->value);

        $stmt = $pdo->prepare('SELECT User.id FROM `User` INNER JOIN `UserGroup` ON User.id = UserGroup.user_id WHERE UserGroup.group_id = :group_id');
        $stmt->execute([':group_id' => $this->id]);
        $this->users = map($stmt->fetchAll(PDO::FETCH_ASSOC), fn($user) => $user['id']);

        $this->oldPermissions = $this->permissions;
        $this->oldUsers = $this->users;
    }

    public static function all(): array
    {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT `id`, `name` FROM `Group`');
        $stmt->execute();
        return map($stmt->fetchAll(PDO::FETCH_ASSOC), fn($group) => new Group($group['id'], $group['name'], [], []));
    }

    public static function byId(int $id): ?Group
    {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT `id`, `name` FROM `Group` WHERE `id` = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) {
            return null;
        }
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Group($group['id'], $group['name'], [], []);
    }

    public static function create(string $name): Group
    {
        $pdo = database();
        $stmt = $pdo->prepare('INSERT INTO `Group` (`name`) VALUES (:name)');
        $stmt->execute([':name' => $name]);
        return new Group($pdo->lastInsertId(), $name, [], []);
    }

    public function delete(): void
    {
        $pdo = database();
        $stmt = $pdo->prepare('DELETE FROM `UserGroup` WHERE `group_id` = :id');
        $stmt->execute([':id' => $this->id]);

        $stmt = $pdo->prepare('DELETE FROM `GroupPermission` WHERE `group_id` = :id');
        $stmt->execute([':id' => $this->id]);

        $stmt = $pdo->prepare('DELETE FROM `Group` WHERE `id` = :id');
        $stmt->execute([':id' => $this->id]);
    }
}