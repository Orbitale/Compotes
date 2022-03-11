Compotes v2
===========

(it's actually v1 because the PHP-based project is v0.x, but it's v2 anyway because it's cooler).

![App screenshot](docs/capture1.png)

## Install

The project is not even in beta for now, but you might find latest nightly builds on the [Actions](https://github.com/Orbitale/Compotes/actions) page, if you check out the latest successful action marked by a "✅" mark (if there is one at least).

## Project setup

If you want to use the in-development project, you can compile it by doing what is explained in the following steps.

### Requirements

* [Rust](https://www.rust-lang.org/tools/install)
* [Node.js](https://nodejs.org/en/download/)
* [Yarn](https://yarnpkg.com/getting-started/install) (preferred by the Tauri team, so expect better compatibility)
* For Linux users:
  * Follow [this guide](https://tauri.studio/docs/get-started/setup-linux#1-system-dependencies) to install the **system dependencies** that are mandatory.
* For Windows users:
  * [Webview2](https://developer.microsoft.com/en-us/microsoft-edge/webview2/#download-section)
* For MacOS users:
  * XCode
  * The GNU C Compiler, installable via `brew install gcc`

### Install

```
yarn install
```

> You do not need to install Rust dependencies, since running `cargo` commands like `cargo run` will automatically download and compile dependencies.

### Run the app in dev mode

```
yarn tauri dev
```

### Compile for production

```
yarn tauri build
```

> This command builds the app in the `src-tauri/target/release/` directory.

## Import a database from v1

* Run `make dump` in the v1 app
* Get the `var/dump_***.sql` file that was just generated
* Install `mysql2sqlite` via this script:
   ```bash
   wget https://raw.githubusercontent.com/dumblob/mysql2sqlite/master/mysql2sqlite
   ```
* Run it on the sql file you got and create your database:
   ```bash
   ./mysql2sqlite dump_***.sql | sqlite3 data.db3
   ```
* Pray it works 🙏
