<!-- docbloc -->
<span id='docbloc'>
DBIntrd - Simple PHP framework for SQLite3 databases. A magic way to automatically create objects and persists data at SQLite3 tables.
<table>
<tr>
<th>Package</th>
<td>intrd/sqlite-dbintrd</td>
</tr>
<tr>
<th>Version</th>
<td>2.0</td>
</tr>
<tr>
<th>Tags</th>
<td>php, sqlite, framework, database</td>
</tr>
<tr>
<th>Project URL</th>
<td>http://github.com/intrd/sqlite-dbintrd</td>
</tr>
<tr>
<th>Author</th>
<td>intrd (Danilo Salles) - http://dann.com.br</td>
<tr>
<th>Copyright</th>
<td>(CC-BY-SA-4.0) 2016, intrd</td>
</tr>
<tr>
<th>License</th>
<td><a href='http://creativecommons.org/licenses/by-sa/4.0'>Creative Commons Attribution-ShareAlike 4.0</a></td>
</tr>
<tr>
<th>Dependencies</th>
<td> &#8226; php >=5.3.0</td>
</tr>
</table>
</span>
<!-- @docbloc 1.1 -->

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

Always use -o to rebuild autoload.
```
Now Composer Autoload will instance the class and you are able to use by this way..

```
require __DIR__ . '/vendor/autoload.php';
use database\dbintrd as db;

$root=dirname(__FILE__)."/"; 
$db_path=$root.'vendor/intrd/sqlite-dbintrd/data/sample.dat'; //path of SQLite sample.dat (sample database included)
$debug=true; //enable SQL queries debug

/*
 * GET all data from table=users
 */
$users = new db("users","all"); 
var_dump($users); //print data

/*
 * GET all data from table=orders + CHILDS data
 */
$users = new db("orders","all",true); 
var_dump($users); //print data

/*
 * GET from tables=users, object where id=40, SET a different email and UPDATE
 */
$user = new db("users",40); 
$user->{0}->email="newmail@dann.com.br"; 
var_dump($user); 
$user->save(true); 

/*
 * CREATE a fresh new object where table=users, SET a email and password and INSERT 
 */
$user = new db("users"); 
$user->email="another@dann.com.br"; 
$user->password="123"; 
var_dump($user);
$user->save(); 

/*
 * GET a object from table=users filtering where email=another@dann.com.br
 */
$users = new db("users","filter:email|another@dann.com.br"); 
var_dump($users); 

/*
 * GET a object from table=users w/ combined filtering (following SQLite sintax)
 */
$users = new db("users","filter:email='another@dann.com.br' and email='asd@dann.com.br'"); 
var_dump($users); //print data

/*
 * GET a object from table=orders filtering and returning CHILDS
 */
$orders = new db("orders","filter:qty|11",TRUE); 
var_dump($orders); 

/**
 * FULL CUSTOM SELECT (following SQLite sintax)
 */
$users = new db("users","custom:SELECT users.email FROM users WHERE id=40",false);

```

## Todo list

* SELECTS are propagating to Childs at application side, do the same at SQLite side w/ a single JOIN query to return child array objects (look src/classes.php nearby line 123) 