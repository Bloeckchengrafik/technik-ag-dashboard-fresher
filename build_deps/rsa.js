import keypair from "keypair";
import fs from "fs";
import { execSync } from "child_process";

export function newstyle_keypair() {
    let keys = keypair()
    let pub = keys.public
    let priv = keys.private

    // save pub to install_data/pub
    fs.writeFileSync("install_data/priv", priv)
    execSync(`openssl rsa -in install_data/priv -pubout -out install_data/new_pub`)
    // read back pub
    pub = fs.readFileSync("install_data/new_pub", "utf8")

    return {
        "public": pub,
        "private": priv
    }
}