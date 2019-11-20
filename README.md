# alpha-orm
An extraordinary javascript database orm

## Features
* Automatically creates tables and columns.
* No configuration required, simply create database.
* Currently supported databases include mysql, sqlite and postgresql.


## Examples
#
### Setup (MySQL)
```php
require_once 'vendor/autoload.php';

use AlphaORM\AlphaORM;

AlphaORM::setup('mysql',[
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'database' => 'alphaorm'
]);
```
#
#
### Creating
```php
/**
* creating
*/
$product = AlphaORM::create('shop_product');
$product->name = "Running Shoes";
$product->price = 1000;
$product->stock = 50;
AlphaORM::store($product);


/**
* creating [foreign key]
*/
$user = AlphaORM::create('user');
$user->firstname = "Claret";
$user->lastname = "Nnamocha";
$user->age = 21;
$user->birthday = '8-October-1998';

$student = AlphaORM::create('student');
$student->matno = "15/31525";
$student->user = user;

AlphaORM::store($student);
```
#
### Reading
```php
/**
* reading [one] (filter)
*/
$product = AlphaORM::find('shop_product','id = :id',[ 'id' => 3 ]);
print_r($product);

/**
* reading [all]
*/
$products = AlphaORM::getAll('shop_product');
print_r($products);


/**
* reading [all] (filter)
*/
$products = AlphaORM::findAll('shop_product','id > 0');
print_r($products);
```
#
### Updating

```php
/**
* update
*/
$product = AlphaORM::find('shop_product','id = :id', [ 'id' => 3 ]);
$product->price = 500;
AlphaORM::store($product);
```
#
### Delete
```php
/**
* delete
*/
$product = AlphaORM::find('shop_product','id = :id', [ 'id' => 2 ]);
AlphaORM::drop($product);
```
### Delete Everything
```php
/**
* delete [all]
*/
AlphaORM::dropAll('shop_product');
```