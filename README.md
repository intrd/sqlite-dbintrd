<!-- @docbloc -->

## System & Composer installation
```
$ sudo apt-get update & apt-get upgrade
$ sudo apt-get install curl php-curl php-cli php-sqlite
$ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

## Usage sample

Assuming your project are running over `Composer` PSR-4 defaults, simply Require it on your `composer.json`
```
"require": {
    "intrd/sqlite-dbintrd": ">=2.0.x-dev <dev-master"
}
```
And run..
```
$ composer install -o #to install
$ composer update -o #to update
```
Always use -o to rebuild autoload.

Now Composer Autoload will instance the class and you are able to use by this way..

```
require __DIR__ . '/vendor/autoload.php';
use sqlite\data as db;

$root=dirname(__FILE__)."/"; //root absolute path
$db_path=$root.'data/sample.dat'; //path of SQLite sample.dat (sample database included)
$debug=true; //enable SQL queries debug

/*
 * GET ALL
 */
$users = new data("users","all"); //GET all data from table=users
vd($users); //print data
```

## Todo list

* SELECTS are propagating to Childs at application side, do the same at SQLite side w/ a single JOIN query to return child array objects (look nearby DBIntrd.php line 160) 