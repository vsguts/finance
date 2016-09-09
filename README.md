Finance system
==============

System allows you to manage cash transactions, calculate balances and view reports.

###### Features:
- Manage transactions
- Multi currencies

###### Technologies
- Written in PHP (>=5.4). Based on Yii 2 Framework.
- MySQL database (via ORM and DAO)
- Bootstrap 3
- Less

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      behaviors/          contains models and controllers behaviors
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      files/              contains uploaded files
      helpers/            contains helper classes
      mail/               contains view files for e-mails
      messages/           contains translations
      migrations/         contains database migrations
      models/             contains model classes
      modules/            contains application modules
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources
      widgets/            contains view widgets



REQUIREMENTS
------------

- PHP: The minimum requirement by this project template that your Web server supports PHP 5.4.0.
- Node with less and phantomjs
  ~~~
  apt-get install nodejs npm
  ln -s /usr/bin/nodejs /usr/bin/node
  npm install -g less
  ~~~


INSTALLATION
------------

Clone repo:

```bash
git clone git@github.com:vsguts/finance.git
```

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Install composer plugin using the following command:

~~~
composer global require "fxp/composer-asset-plugin:~1.1.1"
~~~

Install composer dependencies

~~~
composer install
~~~


CONFIGURATION
-------------

### Check requirements

Console:
```bash
php requirements.php
```

Web:
~~~
http://localhost/finance/requirements.php
~~~

### Database

Copy the file `config/db.php.example` into the `config/db.php` and edit them with real data. For example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=finance',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```

**NOTE:** Application won't create the database for you, this has to be done manually before you can access it.

Use following to create database

```sql
CREATE DATABASE finance CHARACTER SET utf8;
```

Use following to apply migrations:

```bash
./yii migrate
```

Use following to apply user roles:

```bash
./app tools/rbac
```

RUNING
------

You can then access the application through the following URL:

~~~
http://localhost/finance/web/
~~~

To login use folowing:
~~~
login: root@example.com
password: root
~~~
