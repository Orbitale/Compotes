[⬅️ Back to documentation](../README.md)

---

# Install on a dedicated server, VM or VPS

**Note:** Installing Compotes on a dedicated server takes a bit longer because there are more requirements and manual actions are needed.<br>
However, we hope that this guide will help you as much as possible!

Depending on what distribution you use, you will have to follow different steps to have everything availble to make Compotes work.

Whatever the OS, you will need this:

* A web-server (Nginx recommended, but it would work with Apache2 too).
* PHP 7.4 with PHP-FPM. Perfect for Ubuntu 20.04 since it's built-in already! If using older versions of Ubuntu or Debian, you can use the [deb.sury.org](https://deb.sury.org/) repository and install `php7.4` and `php7.4-fpm` in a few commands.<br>
  **Remember that you might also need to install some additional PHP 7.4 extensions.** Running `composer install` will help you know if it's necessary.
* Node.js 10+. Compotes should work with any version above Node 10. Checkout [package manager installation instructions](https://nodejs.org/en/download/package-manager/) on the official Node.js documentation, and if you do not find your distribution (like Debian or Ubuntu which have other installation methods), you can directly go to [installation scripts on Nodesource's repository](https://github.com/nodesource/distributions/blob/master/README.md#installation-instructions) for convenience.
* Mysql 5.7+ or MariaDB 10.1+ with an existing database, and a user that can connect to it.

**Notes:**

* You may clone Compotes using `git clone git@github.com:Orbitale/Compotes.git` and put it in any directory that your web server can point at.
* You need to configure a virtual host on your web server to point on Compotes's `public/` directory, and make sure **every request** is proxied to `public/index.php` via PHP-FPM.<br>
  For this, you may **check out the default configurations** provided in this documentation.<br>
  **Note:** they contain **example** configurations that you **must check out and customize**, like with domain names, or server paths.<br>
  See:
  * [Apache2 vhost example](./prod/apache_vhost.conf) (only for HTTP, you might need to update it for HTTPS).
  * [Nginx vhost example](./prod/nginx_vhost.conf)

Once ready, **run the `make setup-prod` command**.

This script executes several common deployment tasks:

* Install PHP dependencies via Composer.
* Create an `.env.local.php` file to store production environment variables (easiest way to configure them).

**After that, you need to follow the instructions from the script's output** and configure your `ADMIN_PASSWORD` and `DATABASE_URL` environment variables.

If you've done it all, run `bin/deploy.bash`, and you're set!

## Troubleshooting

### How to restart `php-fpm` without `sudo` asking for my password?

Well, follow these steps:

* Run `sudo visudo` (this will edit the `/etc/sudoers` file)
* You might see your username with a directive like this one:
  ```
  your_username      ALL=(ALL:ALL) ALL
  ```
* Below this line, add this line:
  ```
  your_username      ALL=(root) NOPASSWD:/etc/init.d/php7.4-fpm restart
  ```

This will tell `sudo` to not ask for your password when running the specific command you passed to the `NOPASSWD` directive.

---
[⬅️ Back to documentation](../README.md)
