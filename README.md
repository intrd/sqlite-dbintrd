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
use database\data as db;

$root=dirname(__FILE__)."/"; //root absolute path
$db_path=$root.'data/sample.dat'; //path of SQLite sample.dat (sample database included)
$debug=true; //enable SQL queries debug

/*
 * GET ALL
 */
$users = new db::data("users","all"); //GET all data from table=users
var_dump($users); //print data

/*
 * GET ALL w/ CHILDS data
 */
$users = new db::data("orders","all",true); //GET all data from table=orders
var_dump($users); //print data

/*
 * GET, SET and UPDATE...
 */
$user = new db::data("users",40); //CREATE an new object w/ database structure+data(table=users WHERE id=40)
$user->{0}->email="newmail@dann.com.br"; //SET a different email to this user
var_dump($user); //print data
$user->save(true); //UPDATE this object on database (true = UPDATE, null or false = INSERT)

/*
 * SET and INSERT
 */
$user = new db::data("users"); //CREATE a fresh new object (table=users structure without data when second argument is null) 
$user->email="another@dann.com.br"; //setting some data...
$user->password="123"; //setting some data...
var_dump($user);
$user->save(); //INSERT this object on database (null or false = INSERT, true = UPDATE)

/*
 * GET ALL w/ FILTER
 */
$users = new db::data("users","filter:email|another@dann.com.br"); //GET an new object w/ database structure+data(table=users WHERE email=another@dann.com.br)
var_dump($users); //print data

/*
 * GET ALL w/ RAW FILTER
 */
$users = new db::data("users","filter:email='another@dann.com.br' and email='asd@dann.com.br'"); //GET an new object w/ database structure+data(table=users WHERE email=another@dann.com.br and email='asd@dann.com.br')
var_dump($users); //print data

/*
 * GET w/ FILTER and CHILDS data
 */
$orders = new db::data("orders","filter:qty|11",TRUE); 
var_dump($orders); //print data

/**
 * CUSTOM select sample..
 */
 $athletes = new db::data("athletes","custom:SELECT athletes.name,athletes.id,athletes.category FROM athletes WHERE active=1 and category='$category'",false);


```

## Todo list

* SELECTS are propagating to Childs at application side, do the same at SQLite side w/ a single JOIN query to return child array objects (look nearby DBIntrd.php line 160) 