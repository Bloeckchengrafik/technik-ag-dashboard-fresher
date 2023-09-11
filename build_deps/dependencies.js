import CLI from 'clui';
import * as fs from "fs/promises";
import chalk from "chalk";
import {CHECKMARK, CROSSMARK} from "./utils.js";
import commandExists from 'command-exists';
import {exec} from "child_process";

const COMPOSER_INSTALLER = "https://getcomposer.org/installer"

async function install_composer(spinner) {
    // Download composer installer to ../install_data/composer_installer.php
    let file = await fs.open("install_data/composer_installer.php", "w");
    let response = await fetch(COMPOSER_INSTALLER);
    await file.write(await response.text());
    await file.close();

    // Run composer installer with arguments --install-dir=install_data --filename=composer
    return new Promise((resolve, reject) => {
        exec("php install_data/composer_installer.php --install-dir=install_data --filename=composer", (error, stdout, stderr) => {
            if (error) {
                spinner.stop();
                console.error(chalk.redBright(`${CROSSMARK} Composer installation failed!`));
                console.error(chalk.redBright(stderr));
                reject();
            } else {
                resolve();
            }
        });
    });
}


async function check_command(php) {
    return await commandExists(php).then(() => true).catch(() => false);
}

async function check_file(filename) {
    return fs.stat(filename).then(() => true).catch(() => false);
}

export async function check_deps() {
    let spinner = new CLI.Spinner("Checking dependencies...");
    spinner.start();
    await new Promise(resolve => setTimeout(resolve, 500));
    // Check for php executable in PATH
    if (!await check_command("php")) {
        spinner.stop();
        console.error(chalk.redBright(`${CROSSMARK} PHP not found!`));
        process.exit(1);
    } else {
        spinner.stop();
        console.log(chalk.greenBright(`${CHECKMARK} PHP found!`));
        spinner.start();
    }

    // Check for yarn executable in PATH
    if (!await check_command("yarn")) {
        spinner.stop();
        console.error(chalk.redBright(`${CROSSMARK} Yarn not found!`));
        process.exit(1);
    }

    // Check for OpenSSL executable in PATH
    if (!await check_command("openssl")) {
        spinner.stop();
        console.error(chalk.redBright(`${CROSSMARK} OpenSSL not found!`));
        process.exit(1);
    }

    // Check for composer executable in PATH
    if (!await check_file("install_data/composer")) {
        console.error(chalk.redBright(`${CROSSMARK} Composer not found, installing...`));
        spinner.message("Installing composer...");
        await install_composer();
        spinner.stop();
        console.info(chalk.greenBright(`${CHECKMARK} Composer installed!`));
        spinner.start();
        spinner.message("Checking dependencies...");
    } else {
        spinner.stop();
        console.log(chalk.greenBright(`${CHECKMARK} Composer found!`));
        spinner.start();
    }

    spinner.stop();
    console.log(chalk.greenBright(`${CHECKMARK} Dependencies found!`));
}