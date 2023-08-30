<?php

namespace Modules\Email;

use Closure;
use Modules\Login\Permission;
use Modules\Login\User;

class Automailer
{
    public array $users;

    function __construct(
        public View $view,
        public Closure $get_data,
        public string $subject
    )
    {
        $this->users = User::all();
    }

    function filterByPermission(Permission $permission): Automailer
    {
        $this->users = array_filter($this->users, fn($user) => $user->hasPermission($permission));
        return $this;
    }

    function send(): void
    {
        foreach ($this->users as $user) {
            $data = ($this->get_data)($user);
            $html = $this->view->render($data);
            Mailer::send($user->email, $this->subject, $html);
        }
    }
}