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
    ),
    "jwt" => array(
        "payloadbase" => array(
            "iss" => "goe-tec.goethe-projektserver.de",
            "aud" => "goe-tec.goethe-projektserver.de"
        ),
        "expiration" => 3600 * 24, // 1 day
        "privatekey" => <<<EOD
-----BEGIN RSA PRIVATE KEY-----
***
-----END RSA PRIVATE KEY-----
EOD,
        'publickey' => <<<EOD
-----BEGIN PUBLIC KEY-----
***
-----END PUBLIC KEY-----
EOD
    )
);