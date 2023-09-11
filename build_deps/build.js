import CLI from 'clui';
import chalk from "chalk";
import {CHECKMARK} from "./utils.js";
import {execSync} from "child_process";
import fs from "fs/promises";

export async function build_frontend() {
    let spinner = new CLI.Spinner('Building frontend...');
    spinner.start();

    execSync('cd frontend && yarn build', {stdio: 'inherit'});
    spinner.stop();
    console.log(chalk.green(CHECKMARK), 'Frontend built');
}

export async function copy_dir_contents(src, dest) {
    // create destination folder if it doesn't exist
    if (await (fs.stat(dest).catch(() => null)) === null) await fs.mkdir(dest);
    let files = await fs.readdir(src);
    for (let file of files) {
        // if directory, copy recursively
        if ((await fs.stat(`${src}/${file}`)).isDirectory()) {
            await copy_dir_contents(`${src}/${file}`, `${dest}/${file}`);
        } else {
            await fs.copyFile(`${src}/${file}`, `${dest}/${file}`);
        }
    }
}

export async function build() {
    await build_frontend();

    let spinner = new CLI.Spinner('Uniting frontend and backend...');
    spinner.start();

    await fs.rm('build', {recursive: true});
    await fs.mkdir('build');
    await copy_dir_contents('frontend/dist', 'build');
    await copy_dir_contents('backend/src', 'build/api');
    spinner.stop();
    console.log(chalk.green(CHECKMARK), 'Build folder built');
    spinner.start();
    spinner.message('Installing dependencies...');
    execSync('cd build/api && php ../../install_data/composer install', {stdio: 'inherit'});
    await fs.rm('build/api/composer.lock');
    await fs.rm('build/api/composer.json');

    // add a .htaccess file to the build/api/vendor folder to prevent directory listing
    await fs.writeFile('build/api/vendor/.htaccess', 'Deny all');

    spinner.stop();
    console.log(chalk.green(CHECKMARK), 'Dependencies installed');
}