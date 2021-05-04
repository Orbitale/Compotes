Compotes v2
===========

(it's actually v1 because the PHP-based project is v0.x, but it's v2 anyway because it's cooler).

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
