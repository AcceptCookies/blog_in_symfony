#Blog in Symfony

A simple blog in Symfony framework.

## Technical Requirements

1. PHP 8.1 or higher is needed and these PHP extensions: Ctype, iconv, PCRE, Session, SimpleXML, Tokenizer.
2. Also Composer, which is used to install project's dependencies into vendor.

## Setting up the Project
Clone the project to download its contents: 

```bash
$ git clone git@github.com:AcceptCookies/blog_in_symfony.git
```

Install dependencies into vendor folder via composer:
```bash
$ cd blog_in_symfony
$ composer install
```

## Run migrations
Create new database by executing:
```bash
$ symfony doctrine:database:create
```
and run the migration to add the table to the database:
```bash
$ symfony console doctrine:migrations:migrate
```