<?php

// Configure your database connection here

$GLOBALS["config"] = array(
    "mysql" => array(
        "connection" => "mysql:host=localhost:6612;dbname=dev",
        "username" => "root",
        "password" => "root"
    ),
    "mail" => array(
        "host" => "*",
        "port" => 587,
        "username" => "*",
        "password" => "*",
        "from" => "*",
        "fromName" => "*"
    )
);