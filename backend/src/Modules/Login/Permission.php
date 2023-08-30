<?php

namespace Modules\Login;

// This is also automatically used to seed the database using cli.php -p
enum Permission: string
{
    // LOGIN is the permission that allows the user to login
    case Login = "login";
    case ShowAsUser = "showAsUser";
    case ShowAsTechnician = "showAsTechnician";
    case ShowAsManager = "showAsManager";
    case ShowAsAdmin = "showAsAdmin";
    case DenyBooking = "denyBooking";
    case ViewAllEvents = "viewAllEvents";
    case EditAllEvents = "editAllEvents";
    case EditShifts = "editShifts";
    case JoinShifts = "joinShifts";
    case ReceiveAutomailer = "receiveAutomailer";
    case DeactivateEvent = "deactivateEvent";
}
