## DBIntrd - Simple PHP framework for SQLite3 databases

Tired of spending a lot of time manually creating PHP objects and methods(get/set/save..) to connect a SQLite database? 

DBIntrd is magic way to automatically create objects and persists data at SQLite3 tables.

### Package details

* package: DBIntrd
* version: 1.6
* category: framework
* author: intrd - http://dann.com.br
* see: official post - http://dann.com.br/dbintrd-framework-sqlite-to-php-objects/
* copyright: 2015 intrd
* license: Creative Commons Attribution-ShareAlike 4.0 International License - http://creativecommons.org/licenses/by-sa/4.0/
* link: https://github.com/intrd/sqlite-dbintrd/
* Dependencies: 
*   https://github.com/intrd/php-adminer/

### Changelog

* Github commit log: https://github.com/intrd/sqlite-dbintrd/commits/master

## System installation
```
apt-get update & apt-get upgrade
apt-get install git
apt-get install php5-curl php5-sqlite php5-cli
```

## Dependencies installation
```
git clone [all git dependencies, described at header]
ex: git clone https://github.com/intrd/php-adminer/ 
obs: clone at ../ (outside this main directory)

```

### Usage sample (sample.php)
```php
/**
 * sample.php for DBIntrd - Simple SQLite3 PHP Framework
 */

$root=dirname(__FILE__)."/"; //root absolute path
$db_path=$root.'database.dat'; //path of SQLite database.dat (sample database included)
$debug=true; //enable SQL queries debug
require 'dbintrd.php'; //calling DBIntrd Framework

/*
 * GET ALL
 */
$users = new data("users","all"); //GET all data from table=users
vd($users); //print data

/*
 * GET ALL w/ CHILDS data
 */
$users = new data("orders","all",true); //GET all data from table=orders
vd($users); //print data

/*
 * GET, SET and UPDATE...
 */
$user = new data("users",40); //CREATE an new object w/ database structure+data(table=users WHERE id=40)
$user->{0}->email="newmail@dann.com.br"; //SET a different email to this user
vd($user); //print data
$user->save(true); //UPDATE this object on database (true = UPDATE, null or false = INSERT)

/*
 * SET and INSERT
 */
$user = new data("users"); //CREATE a fresh new object (table=users structure without data when second argument is null) 
$user->email="another@dann.com.br"; //setting some data...
$user->password="123"; //setting some data...
vd($user);
$user->save(); //INSERT this object on database (null or false = INSERT, true = UPDATE)

/*
 * GET ALL w/ FILTER
 */
$users = new data("users","filter:email|another@dann.com.br"); //CREATE an new object w/ database structure+data(table=users WHERE email=nhe@dann.com.br, filtering email=nhe@dann.com.br)
vd($users); //print data

/*
 * GET w/ FILTER and CHILDS data
 */
$orders = new data("orders","filter:qty|11",TRUE); 
vd($orders); //print data

/**
 * just a var_dump helper to print out data
 */
function vd($var){ 
  echo"<pre>";var_dump($var);echo"</pre>";
}
```

### Todolist

* SELECTS are propagating to Childs at application side, do the same at SQLite side w/ a single JOIN query to return child array objects (look nearby DBIntrd.php line 160) 