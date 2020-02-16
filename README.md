🍎 Compotes 🍏
=============

A <abbr title="Work in progress">WIP</abbr> application to view bank operations.

This is meant to be used locally, for **your local machine**, not online. That would be quite unfortunate to view all your bank operations in a row.

**🧮**

# Contribute

If you want to contribute, you can directly go to the [Roadmap](#roadmaptodo-list), there are TONS of things you can do.

Some of them are really straightforward!

# Install

This is a PHP/Symfony project, so:

* Make sure you have a working PHP 7.4 setup.
* [Download the Symfony CLI on `symfony.com/download`](https://symfony.com/download) if you do not have it yet, and make sure it is accessible globally.
* Make sure you have [Docker](https://www.docker.com/) installed and accessible globally, as well as Docker Compose.
  > Note: if you do not want to use Docker, then you need a MySQL server and you must change the `DATABASE_URL` value in `.env.local` to point it to your running server. You will also need to install Node.js 12+.
* Run `make install` to install the dependencies (if you do not use Docker, check the `Makefile` to know what commands to execute).
* That's it, it's running! Go to [https://127.0.0.1:8000](https://127.0.0.1:8000), default credentials are `admin`/`admin`.

For any further question on how to customize the setup, feel free to run `make` to list all available commands, or directly check the `Makefile` itself.

# Operations

Operations can be imported **from the backoffice** in the `Import operations` admin panel.

The administration displays all the necessary requirements to make this work. Nothing to add here ☺.

Here are the general requirements:

* File format must be CSV.
* File name must be `{year}-{month}.csv`.
* **First line of the file is ignored**, so it can contain headers or an empty line or anything.
* Each line column must be the following, **in this order**:
  1. Date in `d/m/Y` format (nothing else supported for now).
  2. Operation type (given by bank provider, can be an empty string).
  3. Display type (not mandatory, can be an empty string).
  4. Operation details (can be a longer string).
  5. Amount of the operation. Can be a negative number.
     > Note that this can be a string like `1 234,55` or `1,234.55`. Every non-digit character (except `+` and `-`) will be removed and all remaining numbers will make the amount **in cents**. Do not store floats to avoid [floating point issues](https://0.30000000000000004.com/) (read this link, if you need to know why you should avoid storing floats).

# Set up the administration panel

The default **username** for the admin panel is `admin`, but you can change it by overriding the `ADMIN_USER` environment variable in your `.env.local` file.

If you want to change the password, you can run the `make admin-password` command and inject the password as an environment variable, like this:

```
$ make -e DEFAULT_ADMIN_PASSWORD=yourpassword admin-password
[PHP] Overwrite existing password in ".env.local"
```

The password will be saved inside an `.env.local` file at the root of the project, which may look like this:

```
# .env.local

# Set to "admin" by default, but could be overriden to anything you like with the "make admin-password" command.
ADMIN_PASSWORD='$argon2id$v=19$m=65536,t=4,p=1$N0R4Zi5hUWQ3QXB0bjVGdg$VsVcHzGRfGPlEbLo/JK0M4S0QT5Mx7wd+vbwXanjpb8'

# Not here by default, but you can override it, if you need.
ADMIN_USER=admin

```

**Important note:** do not forget to add single quotes `'` around the hashed password, else the `$` sign will cause issues (or you can also escape it with `\$` instead of `$`).

# Roadmap/TODO list

Feel free to contribute 😉.

* Make many analytics dashboards (that's what this app is for in the first place, with Highcharts).
* Support JS closures in Chart objects (by using a placeholder to remove quotes maybe?).
* Add a lot of fixtures to play with.
* Add translations for tags (maybe using an extension like gedmo or knp?).
* Implement more source file types like xls, ods, etc., that could be transformed to CSV before importing them. [PHPSpreadsheet](https://phpspreadsheet.readthedocs.io/) is already installed, though not used yet.
* Custom source file format (only CSV for now, and that's perfectly fine for me).
* Change CSV headers at runtime (in the command-line integration).
* Change CSV delimiter/enclosure at runtime (in the command-line integration).
* Change input operation date format (for now it's `d/m/Y H:i:s O` as of French date format).
* Multiple bank accounts (needs to migrate all existing data to a "default" account create via migration).
* Operation currency (a single header, maybe a default value in the importer and the command too).
* Add tests (this is a continuous task anyway ☺ ).
* Support for Bills (a Bill object and a BillItem one, and maybe associate a Bill with one or more Operation objects with a OneToMany relationship so multiple operations can associate with one bill).
* Support for "sub-operations" (to allow cutting an operation in multiple sub-operations so we can get the "details" of an operation, each sub-operation should also be able to be tagged).
* Imagine a support for Sylius bundles?

**✔️ Done already:**

* Operation tags (insurance, internet provider, car loan, etc.). Multiple tags per operation.
* Demo app at https://piers.ovh/compotes/ with credentials `admin`/`admin` and database reset every day.
* Added default tags (in French only for now).
* Docker setup with Compose.
* Added tons of other commands to the Makefile.
* Made a first PoC for the analytics dashboard.
* Allow to upload Operations files directly from the backoffice. It even has a nice drag&drop integration to customize line column fields!
* Change CSV headers at runtime (done only for the backoffice integration).
* Change CSV delimiter/enclosure at runtime (done only for the backoffice integration).

# License

Project is developed under AGPL-3.0 license. Check the [LICENSE](LICENSE) file for more information.

---

**🍎🥝🍏**
