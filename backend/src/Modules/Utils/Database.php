<?php

namespace Modules\Utils;

use PDO;

$pdo = new PDO(
    $GLOBALS["config"]["mysql"]["connection"],
    $GLOBALS["config"]["mysql"]["username"],
    $GLOBALS["config"]["mysql"]["password"],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function database(): PDO
{
    global $pdo;
    return $pdo;
}