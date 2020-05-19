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

REQUIREMENTS
------------

- Docker
- Docker compose

INSTALLATION
------------

Clone repo:

```bash
git clone git@github.com:vsguts/finance.git
```

Prepare configs
~~~
cp docker-compose-local.yml.dist docker-compose.yml
cp .env.dist .env
~~~

Edit configs.

Run docker containers:
~~~
docker-compose up -d
~~~

Install composer plugin using the following command:

~~~
docker-compose exec php composer require "fxp/composer-asset-plugin:^1.3.1"
~~~

Install composer dependencies

~~~
docker-compose exec php composer install
~~~

Check requirements (if necessary):

~~~
docker-compose exec php php requirements.php
~~~

Restore DB dump (if necessary):

~~~
docker-compose exec php ./app migrate/up --migrationPath=migrations/dump --interactive=0
~~~

Apply DB migrations (if necessary):

~~~
docker-compose exec php ./app migrate/up --interactive=0
~~~

Apply user roles:

~~~
docker-compose exec php ./app rbac/init
~~~

RUNNING
-------

You can then access the application through the following URL:

~~~
http://localhost:8080/
~~~

To login use folowing:
~~~
login: root@example.com
password: root
~~~
