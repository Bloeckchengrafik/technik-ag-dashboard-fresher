import {readFileSync, writeFileSync} from "fs";
import CLI from 'clui';
import chalk from "chalk";
import {CHECKMARK} from "./utils.js";
import {newstyle_keypair} from "./rsa.js";

export async function regenerate_configurations(cfg) {
    let spinner = new CLI.Spinner("Generating keypair...");
    spinner.start();
    await new Promise(resolve => setTimeout(resolve, 500));
    let keys = newstyle_keypair();
    spinner.stop();
    console.info(chalk.greenBright(`${CHECKMARK} Keypair generated!`));
    spinner.start();
    spinner.message("Building frontend configuration...");

    let backend = "VITE_BACKEND=/api/";

    let padding = " ".repeat("VITE_BACKEND_JWT_PUBKEY=\"".length);
    let pubkey = keys.public
        .replace("BEGIN RSA PUBLIC KEY", "BEGIN PUBLIC KEY")
        .replace("END RSA PUBLIC KEY", "END PUBLIC KEY")
        .trimEnd()
        .replace(/\n/g, `\n${padding}`);
    let pubkey_env = `VITE_BACKEND_JWT_PUBKEY="${pubkey}"\n`;

    let dotenv = `${backend}\n${pubkey_env}`; // frontend/.env
    // delete frontend/.env if it exists
    if (readFileSync("frontend/.env", "utf-8")) {
        writeFileSync("frontend/.env", "");
    }
    writeFileSync("frontend/.env", dotenv);
    spinner.stop();
    console.info(chalk.greenBright(`${CHECKMARK} Frontend configuration built!`));
    spinner.start();
    spinner.message("Building backend configuration...");

    let backend_config = `
<?php
// AUTOGENERATED FILE ON ${new Date().toISOString()} - DO NOT EDIT

$GLOBALS["config"] = array(
    "mysql" => array(
        "connection" => "mysql:host=${cfg.database.host}:${cfg.database.port};dbname=${cfg.database.database}",
        "username" => "${cfg.database.user}",
        "password" => "${cfg.database.password}"
    ),
    "mail" => array(
        "host" => "${cfg.mail.host}",
        "port" => ${cfg.mail.port},
        "username" => "${cfg.mail.user}",
        "password" => "${cfg.mail.password}",
        "from" => "${cfg.mail.from}",
        "fromName" => "${cfg.mail.fromName}"
    ),
    "jwt" => array(
        "payloadbase" => array(
            "iss" => "${cfg.jwt.issuer}",
            "aud" => "${cfg.jwt.audience}",
        ),
        "expiration" => ${cfg.jwt.expiration},
        "privatekey" => <<<EOD
${keys.private.trimEnd()}
EOD,
        'publickey' => <<<EOD
${keys.public.trimEnd()}
EOD
    )
);
`;

    writeFileSync("backend/src/Modules/Config/Config.php", backend_config);
    spinner.stop();
    console.info(chalk.greenBright(`${CHECKMARK} Backend configuration built!`));
}

export async function repair_dev_env() {
    // Replace the first line of frontend/.env with VITE_BACKEND=http://localhost:8000/
    const env = readFileSync("frontend/.env", "utf-8");
    const lines = env.split("\n");
    lines[0] = "VITE_BACKEND=http://localhost:8000/";
    writeFileSync("frontend/.env", lines.join("\n"));
}