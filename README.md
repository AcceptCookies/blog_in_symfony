# Blog in Symfony

A simple blog in Symfony framework.

## Technical Requirements

1. PHP 8.1 or higher is needed and these PHP extensions: Ctype, iconv, PCRE, Session, SimpleXML, Tokenizer.
2. Also Composer, which is used to install project's dependencies into vendor.

## Setting up the Project
Clone the project to download its contents: 

```bash
git clone git@github.com:AcceptCookies/blog_in_symfony.git
```

Install dependencies into vendor folder via composer:
```bash
cd blog_in_symfony
composer install
```

## Migrations: Creating the Database Tables/Schema
Create new database by executing:
```bash
symfony console doctrine:database:create
```
generate the migration with command:
```bash
symfony console make:migration
```
and update database by executing migrations:
```bash
symfony console doctrine:migrations:migrate
```

## Populate Database Tables

Automatically generate data and fill your database tables with test data by executing this command:

```bash
symfony console doctrine:fixtures:load
```
> **_NOTE:_** By default the load command purges the database, removing all data from every table. 
> To append your fixtures' data add the --append option.

## Start the Web Server

To start a local server navigate to root directory and run command:

```bash 
symfony server:start
```

By default server is listening on http://127.0.0.1:8000 

## Unit Testing

This command automatically runs application tests:

```bash
symfony php bin/phpunit
```

or runs specific folder by: 

```bash
symfony php bin/phpunit tests/PostControllerTest.php
```
