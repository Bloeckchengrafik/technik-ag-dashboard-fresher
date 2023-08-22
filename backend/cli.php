#!/usr/bin/env php
<?php

include_once 'src/Modules/Autoload.php';

use function Modules\Utils\database;
use Modules\Login\Permission;

$sqlDrop = <<<SQL
DROP TABLE IF EXISTS Email;
DROP TABLE IF EXISTS StudentInformation;
DROP TABLE IF EXISTS AuthKey;
DROP TABLE IF EXISTS User;
SQL;

$sqlCreate = <<<SQL
CREATE TABLE IF NOT EXISTS User (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Permission (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS UserPermission (
    user_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (permission_id) REFERENCES Permission(id),
    PRIMARY KEY (user_id, permission_id)
);

CREATE TABLE IF NOT EXISTS `Group` (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS UserGroup (
    user_id INTEGER NOT NULL,
    group_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (group_id) REFERENCES `Group`(id),
    PRIMARY KEY (user_id, group_id)
);

CREATE TABLE IF NOT EXISTS GroupPermission (
    group_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES `Group`(id),
    FOREIGN KEY (permission_id) REFERENCES Permission(id),
    PRIMARY KEY (group_id, permission_id)
);

CREATE TABLE IF NOT EXISTS AuthKey (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    `key` TEXT NOT NULL UNIQUE,
    method TEXT NOT NULL,
    once_only BOOLEAN NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(id)
);

CREATE TABLE IF NOT EXISTS Email
(
    email_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    receiver varchar(255) NOT NULL,
    subject  varchar(255) NOT NULL,
    content  TEXT         NOT NULL
);

CREATE TABLE IF NOT EXISTS StudentInformation
(
    user_id INTEGER PRIMARY KEY,
    year INTEGER NOT NULL,
    tutor TEXT NOT NULL,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(id)
);
SQL;


$isHelp = getopt('h', ['help']) || $argc === 1;

if ($isHelp) {
    echo <<<HELP
Usage: cli.php [options]

Options:
    -h, --help       Show this help message and exit.
    -d, --drop       Drop database before initializing.
    -p, --permission Update permission descriptor table.
    -a, --all        Drop database, initialize, and update permission table.
    -i, --init       Initialize database.

HELP;
    exit;
}

$drop = getopt('d', ['drop']);
$init = getopt('i', ['init']);
$permission = getopt('p', ['permission']);
$all = getopt('a', ['all']);

if ($all) {
    $drop = true;
    $init = true;
    $permission = true;
    echo "Dropping database, initializing, and updating permission table...\n";
}

if ($drop) {
    database()->exec($sqlDrop);
    echo "Database dropped.\n";
}

if ($init) {
    database()->exec($sqlCreate);
    echo "Database initialized.\n";
}

if ($permission) {
    $perms = Permission::cases();
    $to_delete = database()->query("SELECT name FROM Permission")->fetchAll(PDO::FETCH_COLUMN);
    $sql = "INSERT IGNORE INTO Permission (name) VALUE (?)";

    $stmt = database()->prepare($sql);
    for ($i = 0; $i < count($perms); $i++) {
        $perm = $perms[$i]->value;
        $stmt->execute([$perm]);
        $to_delete = array_diff($to_delete, [$perm]);
    }

    $sql = "DELETE FROM Permission WHERE name = ?";
    $stmt = database()->prepare($sql);
    foreach ($to_delete as $perm) {
        $stmt->execute([$perm]);
    }

    echo "Permission table updated.\n";
}