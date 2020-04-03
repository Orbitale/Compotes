## v0.9.1

* Fix issue preventing filtering the list of operations while using the search engine

## v0.9.0

* Added automatic Heroku installer, so you can `git clone ...` and `make heroku-init` just to create your own instance! 🚀
* Made Tag entity translatable and configured translations in EasyAdmin's backoffice.
* Updated all PHP and NodeJS dependencies.
* Simplified deploy script a bit.
* Added Docker images for PHP and for the full app! Documentation will come soon :)
* Fix issue with YearMonthComparison chart where data wasn't properly sorted with months.

## v0.8.0 (08 Mar 2020)

* Added base support for Bank Accounts.
* Fixed "delete" action still accessible on some pages.
* Fixed "import" page that was accessible even when not logged in.
* Added a DTO proof of concept for EasyAdmin.

## v0.7.1 (05 Mar 2020)

* You can now fund the development of Compotes! Click on the `💜Sponsor` button on the repository!
* Add a new chart for yearly-months balance comparison
* A new `make open` command has been added so you can look awesome when starting the project, like by doing `make start open` 🚀 

## v0.7.0 (16 Feb 2020)

* Big change: the `operations:import` console command has been removed. Use the admin backoffice now instead, because it's way more customizable (and easier to customize by the way).
* A `bin/deploy.bash` script to use when you deploy Compotes anywyhere
* There's now a `resources/example_operation_file.ods` file you can use if you want to create your own CSV files to import operations
* Changed analytics rendering just a bit, and added two new charts for yearly and monthly balance (more charts to come!)
* We can use "predefined" date filters in the analytics dashboard!
* We can filter operations by tags in the operations list!
* Added a big "triage" system, the goal is to avoid duplicate operations when importing new files, and this feature will soon be used in the "import" feature so we can import the same files multiples without having duplicates, in case of mistakes. This system needs a new `hash` and `state` properties in the `Operation` entity.

Also, a lot of translations have been added in `messages.en.yml`, and I received [a question on Twitter](https://twitter.com/GabiUdrescu/status/1227923475957997568) about future translations: this project will definitely have to be translated, and I like @gabiudrescu's proposal! Feel free to contribute before him if you like this idea 😄 

## v0.6.0 (12 Feb 2020)

* Now, we can import Operations directly from the backoffice! The internal system has been refactored to be customizable, and a simple Form has been create for that purpose in the new `Import operation` admin panel. 🗃 
* A test setup has been added with PHPUnit, there are a few tests already for static pages, and the test setup is added to the Github Actions CI. More tests to come! Maybe you want to contribute? 👁 
* The test setup is advanced enough to have its own database (not 100% compatible with Symfony CLI so it was customized a bit), so you can be free of having different envs for your project and different databases, all separated with joy. 😸 
* Added a `draggable.ts` file to customize the drag&drop integration in the Operations Import page, it's our first Typescript file and it's so cool to write vanilla-purpose code with Typescript. ❤️ 

## v0.5.0 (03 Feb 2020)

* More fixtures to play with
* Add a second chart for tags amount in analytics page
* Add date filters to analytics

This is the "PoC" I wanted 👍 

## v0.4.0 (02 Feb 2020)

* Some fixes & backend capabilities adjustments
* Better docs and installation setup
* Update dependencies
* Added one chart in the new "analytics" page and it's lovely 📊 

## v0.3.0 (15 Jan 2020)

* Fixed and enhanced CI
* Add fixtures to get default Tags (imported from an existing bank account system)
* Add more things to the roadmap
* Added `Tag::$parent` property (for simple tree)
* Better display of tags in listing with small badges

## v0.2.0 (23 Dec 2019)

* Fix some bugs
* Add a small file as "sample data" for how exports are supposed to be formatted
* Add more details when tag rules are applied
* Add a Makefile with a default setup for this project
* Force "EXPLICIT" tracking policy in all entities (might be brand new for some people, so let's do it 😄 )
* Add CS and some QA tools

## v0.1.0 (09 Dec 2019)

Base release with these features:

* Login (with the need of a self-generated password, for safety)
* Import operations from command-line
* Admin panel with [CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) for:
  * Bank operations (will be restricted in the future
  * Operation tags
  * Rules to apply tags to certain operations depending on a matching label pattern (regex allowed)
* Apply tag rules to operations from the command-line or from the admin panel

