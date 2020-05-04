[⬅️ Back to documentation](../README.md)

---

### Install on Heroku

To install on Heroku, here are the requirements:

* [Create an account on Heroku](https://signup.heroku.com/).
* Install the [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli).
* Run `heroku login` as stated in the docs, to be able to create projects from the command line.
* Have `git` installed.
* Make sure you have **PHP 7.4** installed on your machine (it is used to generate the administration panel's password).

If you fulfill all these, all you have to do is:

* Clone the Compotes project on your machine.
* Run `make setup-heroku`.
* Done!

Once the project is created, you can open it via `heroku open`, with the password you specified during the installation command.

If you encounter **any trouble**, please post a new Issue on the project, we will be glad to help!

**Notes:**
* If you do not have `make`, you can still run the same command via `heroku/create_project` with `bash`.
  However, and even for Windows users, you can still have `make` on your machine by installing MinGW or Chocolatey.
* This command is **interactive**, so you cannot automatize the creation of a project. The reason is that the password **must** be specified, at least for security reasons. Using the default `admin` password is insecure, and generating a random password might be too cumbersome for users now.
* This creates a server instance with the `eu` region, not changeable for now.
* It uses JawsDB MySQL with the verson 5.7, for now. If the project becomes compatible with more versions, we will update the project 😉.

---
[⬅️ Back to documentation](../README.md)
