⚠ This app is being rewritten to simplify installation for end-users. The work is ongoing in the [rewrite](https://github.com/Orbitale/Compotes/tree/rewrite) branch.

---

![](https://github.com/Orbitale/compotes/workflows/Docker%20Image/badge.svg)
![](https://github.com/Orbitale/compotes/workflows/Node.js/badge.svg)
![](https://github.com/Orbitale/compotes/workflows/PHP/badge.svg)


🍎 Compotes 🍏
=============

A small application to visualise bank operations in graphs and plots.

**🧮**

![Compotes screenshot](./doc/assets/compotes_screenshot.png)

---

# Documentation index

* [Contribute](#contribute)
* [Install](#install)
  * [Install on Heroku](./doc/install_heroku.md)
  * [Install on a dedicated server, VM or VPS](./doc/install_server.md)
    * [Troubleshooting](./doc/install_server.md#troubleshooting)
    * [How to restart `php-fpm` without `sudo` asking for my password?](./doc/install_server.md#how-to-restart-php-fpm-without-sudo-asking-for-my-password)
* [Operations](#operations)
* [Set up the administration panel](#set-up-the-administration-panel)
* [Project's roadmap](#roadmaptodo-list)

---

# Contribute

If you want to contribute, you can directly go to the [Roadmap](#roadmaptodo-list), there are TONS of things you can do.

Some of them are really straightforward!

# Install

**Note:** This project is meant to be used **locally**. Put it online **at your own risk**, since these may contain sensitive information of your own.

This is a PHP/Symfony project, so:

* Make sure you have a working PHP 7.4 setup.
* [Download the Symfony CLI on `symfony.com/download`](https://symfony.com/download) if you do not have it yet, and make sure it is accessible globally.
* Make sure you have [Docker](https://www.docker.com/) installed and accessible globally, as well as Docker Compose.
  > Note: if you do not want to use Docker, then you need a MySQL server and you must change the `DATABASE_URL` value in `.env.local` to point it to your running server. You will also need to install Node.js 12+.
* Run `make install` to install the dependencies (if you do not use Docker, check the `Makefile` to know what commands to execute).
* That's it, it's running! 
 * At this stage, you can go to [https://127.0.0.1:8000](https://127.0.0.1:8000), default credentials are `admin`/`admin`.
 * Or you can also run `make open` and enjoy :)

For any further question on how to customize the setup, feel free to run `make` to list all available commands, or directly check the `Makefile` itself.

## Install Compotes on services

Compotes is made to be self-hostable!

We provide built-in solutions for easy setups and faster deployments.

### [Install on Heroku](./doc/install_heroku.md)
### [Install on a dedicated server, VM or VPS](./doc/install_server.md)

# Operations

The concept of `Operation` is omnipresent in Compotes.

An operation represents a single bank account operation that you must have imported from your bank data.

Check the [operations documentation](./doc/operations.md) for more.

# Set up the administration panel

As everything is made on Compotes for a quick setup, there might be things you want to change, such as the **administration password**.

Check the [admin panel documentation](./doc/setup_admin.md) for more.

# Roadmap/TODO list

Feel free to contribute 😉.

* (Easy pick!) Make many **more analytics dashboards** (that's what this app is for in the first place, with Highcharts).
* Use the "triage" feature in the "import file" command.
* Allow a **preview of an imported CSV file** (really important!).
* Support JS closures in Chart objects (by using a placeholder to remove quotes maybe?).
* Add **tags translations** (maybe using an extension like gedmo or knp?).
* Implement more source file types like xls, ods, etc., that could be transformed to CSV before importing them. [PHPSpreadsheet](https://phpspreadsheet.readthedocs.io/) is already installed, though not used yet.
* Custom source file format (only CSV for now, and that's perfectly fine for me).
* Change input **operation date format** (for now it's `d/m/Y H:i:s O` as of French date format).
* Add **tests** (this is a continuous task anyway ☺ ).
* Support for **Bills** (a Bill object and a BillItem one, and maybe associate a Bill with one or more Operation objects with a OneToMany relationship so multiple operations can associate with one bill).
* Support for **sub-operations** (to allow cutting an operation in multiple sub-operations so we can get the "details" of an operation, each sub-operation should also be able to be tagged).
* Imagine a support for Sylius bundles?

**✔️ Done already:**

* Operation tags (insurance, internet provider, car loan, etc.). Multiple tags per operation.
* Demo app at https://piers.ovh/compotes/ with credentials `admin`/`admin` and database reset every day.
* Added default tags (in French only for now).
* Docker setup with Compose.
* Added tons of other commands to the Makefile.
* Made a first PoC for the analytics dashboard.
* Allow to upload Operations files directly from the backoffice. It even has a nice drag&drop integration to customize line column fields!
* Change CSV headers at runtime.
* Change CSV delimiter/enclosure at runtime.
* Add a lot of fixtures to play with.
* Removed the "import from command line" feature. Using EasyAdmin allows more customization.
* Added a big "triage" system to avoid having duplicates.
* Multiple bank accounts (needs to migrate all existing data to a "default" account created via migration).
* Operation currency (added via the bank account in the end).
* Heroku auto-install script

# License

Project is developed under AGPL-3.0 license. Check the [LICENSE](LICENSE) file for more information.

---

**🍎🥝🍏**
