#!/usr/bin/env php
<?php

include_once 'src/Modules/Autoload.php';

use function Modules\Utils\database;
use Modules\Login\Permission;

$sqlDrop = file_get_contents("sql/drop.sql");
$sqlCreate = file_get_contents("sql/create.sql");

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