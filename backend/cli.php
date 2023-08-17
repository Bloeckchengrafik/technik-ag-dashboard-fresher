#!/usr/bin/env php
<?php

use function Modules\Utils\database;

include_once 'src/Modules/Autoload.php';

$sqlDrop = <<<SQL
DROP TABLE IF EXISTS EMAIL;
DROP TABLE IF EXISTS AuthKey;
DROP TABLE IF EXISTS User;
SQL;

$sqlCreate = <<<SQL
CREATE TABLE IF NOT EXISTS User (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    permission INTEGER NOT NULL
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
SQL;


$isHelp = getopt('h', ['help']) || $argc === 1;

if ($isHelp) {
    echo <<<HELP
Usage: cli.php [options]

Options:
    -h, --help  Show this help message and exit.
    -d, --drop  Drop database before initializing.
    -i, --init  Initialize database.

HELP;
    exit;
}

$drop = getopt('d', ['drop']);
$init = getopt('i', ['init']);

if ($drop) {
    database()->exec($sqlDrop);
    echo "Database dropped.\n";
}

if ($init) {
    database()->exec($sqlCreate);
    echo "Database initialized.\n";
}
