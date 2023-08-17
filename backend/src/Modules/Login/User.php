<?php
namespace Modules\Login;

class User {
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $hash;
    public int $permission;

    public function __construct(int $id, string $firstname, string $lastname, string $email, string $hash, int $permission)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->hash = $hash;
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
}