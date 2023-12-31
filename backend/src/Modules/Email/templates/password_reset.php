<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body {
            font-family: sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto 0;
        }

        .logo {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo img {
            height: 50px;
        }

        .logo span {
            font-size: 20px;
            font-weight: bold;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        p {
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0069d9;
        }

        .btn-container {
            text-align: center;
        }

        .key {
            padding: 1em;
            margin: 1em;
            background-color: #e0e0e0;
        }

    </style>
</head>
<body>
<?php

use Modules\Login\User;

/* @var $user User */
/* @var $key string */
?>
<div class="container">
    <div class="logo"><img src="<?php
        // get current host
        $root = $_SERVER['HTTP_HOST'];
        // get current protocol
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
        echo $protocol . '://' . $root . '/goetec.png';
        ?>" alt="logo"><br/>
        <span>GoeTec Dashboard</span>
    </div>
    <h1>Hallo <?= $user->firstname ?>,</h1>
    <p>
        wir haben eine Anfrage erhalten, um dein Passwort zur&uuml;ckzusetzen.
        Wenn du diese Anfrage nicht gestellt hast, kannst du diese E-Mail ignorieren.

        Um dein Passwort zur&uuml;ckzusetzen, gebe bitte den folgenden Code in der App ein:
    </p>
    <br/>
    <p class="btn-container">
        <span class="key">
            <?= $key ?>
        </span>
    </p>
    <br/>
    <p>
        Mit freundlichen Gr&uuml;&szlig;en<br/>
        GoeTec AG
    </p>
</div>

</body>
</html>