[⬅️ Back to documentation](../README.md)

---

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

---
[⬅️ Back to documentation](../README.md)
