<?php

namespace Modules\Login;

// This is also automatically used to seed the database using cli.php -p
enum Permission: string
{
    // LOGIN is the permission that allows the user to login
    case LOGIN = "login";
    case ShowAsUser = "showAsUser";
    case ShowAsTechnician = "showAsTechnician";
    case ShowAsManager = "showAsManager";
    case ShowAsAdmin = "showAsAdmin";
}
