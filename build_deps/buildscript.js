import * as fs from "fs/promises";
import chalk from 'chalk';
import {check_deps} from "./dependencies.js";
import {regenerate_configurations, repair_dev_env} from "./gencfg.js";
import {build} from "./build.js";

let buildDataDir = "build";
let installDataDir = "install_data";
let configFile = "config.json";

//type:
/*
{
    database: {
        host: string,
        port: number,
        user: string,
        password: string,
        database: string
    },
    mail: {
        host: string,
        port: number,
        user: string,
        password: string,
        from: string,
        fromName: string
    },
    jwt: {
        issuer: string,
        audience: string,
        expiration: number,
    }
}
 */
let config = null;

async function main() {
    // Make sure there are config file, build data dir and install data dir
    await fs.mkdir(buildDataDir, {recursive: true});
    await fs.mkdir(installDataDir, {recursive: true});

    // Error out if there is no config file
    if (!await fs.stat(configFile).then(() => true).catch(() => false)) {
        console.error(chalk.redBright("No config file found!"));
        return;
    }

    // Read config file
    config = JSON.parse(await fs.readFile(configFile, "utf-8"));
    await check_deps();
    await regenerate_configurations(config);
    await build();

    await repair_dev_env();
}

main().then();
