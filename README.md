# Nginx PHP MySQL Laravel

Docker running Nginx, PHP-FPM, Composer, MySQL, PHPMyAdmin and Laravel

## Overview

1. [Install prerequisites](#install-prerequisites)

    Before installing project make sure the following prerequisites have been met.

2. [Clone the project](#clone-the-project)

    We’ll download the code from its repository on GitHub.

3. [Run the application](#run-the-application)

    By this point we’ll have all the project pieces in place.

4. [Use Makefile](#use-makefile) [`Optional`]

    When developing, you can use `Makefile` for doing recurrent operations.

5. [Use Docker Commands](#use-docker-commands)

    When running, you can use docker commands for doing recurrent operations.

___

## Install prerequisites

To run the docker commands without using **sudo** you must add the **docker** group to **your-user**:

```
sudo usermod -aG docker your-user
```

For now, this project has been mainly created for Unix `(Linux/MacOS)`. Perhaps it could work on Windows.

All requisites should be available for your distribution. The most important are :

* [Git](https://git-scm.com/downloads)
* [Docker](https://docs.docker.com/engine/installation/)
* [Docker Compose](https://docs.docker.com/compose/install/)

Check if `docker-compose` is already installed by entering the following command : 

```sh
which docker-compose
```

Check Docker Compose compatibility :

* [Compose file version 3 reference](https://docs.docker.com/compose/compose-file/)

The following is optional but makes life more enjoyable :

```sh
which make
```

On Ubuntu and Debian these are available in the meta-package build-essential. On other distributions, you may need to install the GNU C++ compiler separately.

```sh
sudo apt install build-essential
```

### Images to use

* [Nginx](https://hub.docker.com/_/nginx/)
* [MySQL](https://hub.docker.com/_/mysql/)
* [PHP-FPM](https://hub.docker.com/r/r0drigocarvalho/php-fpm/)
* [Composer](https://hub.docker.com/_/composer/)
* [PHPMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin/)
* [Generate Certificate](https://hub.docker.com/r/jacoelho/generate-certificate/)

You should be careful when installing third party web servers such as MySQL or Nginx.

This project use the following ports :

| Server     | Port |
|------------|------|
| MySQL      | 8989 |
| PHPMyAdmin | 8080 |
| Nginx      | 8000 |
| Nginx SSL  | 3000 |

___

## Clone the project

To install [Git](http://git-scm.com/book/en/v2/Getting-Started-Installing-Git), download it and install following the instructions :

```sh
git clone https://github.com/r0drigocarvalho/docker-nginx-php-mysql-laravel
```

Go to the project directory :

```sh
cd docker-nginx-php-mysql-laravel
```

### Project tree

```sh
.
├── Makefile
├── README.md
├── data
│   └── db
│       ├── dumps
│       └── mysql
├── doc
├── docker-compose.yml
├── etc
│   ├── nginx
│   │   ├── default.conf
│   │   └── default.template.conf
│   ├── php
│   │   └── php.ini
│   └── ssl
└── web
    ├── app
    └── composer.json
```

___


## Run the application

1. Copying the composer configuration file : 

    ```sh
    cp web/app/composer.json.dist web/app/composer.json
    ```

2. Start the application :

    ```sh
    docker-compose up -d
    ```

    **Please wait this might take a several minutes...**

    ```sh
    docker-compose logs -f # Follow log output
    ```

3. Open your favorite browser :

    * [http://localhost:8000](http://localhost:8000/)
    * [http://localhost:8080](http://localhost:8080/) PHPMyAdmin (username: dev, password: dev)

4. Stop and clear services

    ```sh
    docker-compose down -v
    ```

___

## Use Makefile

When developing, you can use [Makefile](https://en.wikipedia.org/wiki/Make_(software)) for doing the following operations :

| Name          | Description                                  |
|---------------|----------------------------------------------|
| apidoc        | Generate documentation of API                |
| clean         | Clean directories for reset                  |
| code-sniff    | Check the API with PHP Code Sniffer (`PSR2`) |
| composer-up   | Update PHP dependencies with composer        |
| docker-start  | Create and start containers                  |
| docker-stop   | Stop and clear all services                  |
| gen-certs     | Generate SSL certificates for `nginx`        |
| logs          | Follow log output                            |
| mysql-dump    | Create backup of all databases               |
| mysql-restore | Restore backup of all databases              |
| phpmd         | Analyse the API with PHP Mess Detector       |
| test          | Test application with phpunit                |

### Examples

Start the application :

```sh
make docker-start
```

Show help :

```sh
make help
```

___

## Use Docker commands

### Installing package with composer

```sh
docker run --rm -v $(pwd)/web/app:/app composer require symfony/yaml
```

### Updating PHP dependencies with composer

```sh
docker run --rm -v $(pwd)/web/app:/app composer update
```

### Generating PHP API documentation

```sh
docker run --rm -v $(pwd):/data phpdoc/phpdoc -i=vendor/ -d /data/web/app/src -t /data/web/app/doc
```

### Testing PHP application with PHPUnit

```sh
docker-compose exec -T php ./app/vendor/bin/phpunit --colors=always --configuration ./app
```

### Fixing standard code with [PSR2](http://www.php-fig.org/psr/psr-2/)

```sh
docker-compose exec -T php ./app/vendor/bin/phpcbf -v --standard=PSR2 ./app/src
```

### Checking the standard code with [PSR2](http://www.php-fig.org/psr/psr-2/)

```sh
docker-compose exec -T php ./app/vendor/bin/phpcs -v --standard=PSR2 ./app/src
```

### Analyzing source code with [PHP Mess Detector](https://phpmd.org/)

```sh
docker-compose exec -T php ./app/vendor/bin/phpmd ./app/src text cleancode,codesize,controversial,design,naming,unusedcode
```

### Checking installed PHP extensions

```sh
docker-compose exec php php -m
```

### Handling database

#### MySQL shell access

```sh
docker exec -it mysql bash
```

and

```sh
mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD"
```

#### Creating a backup of all databases

```sh
mkdir -p data/db/dumps
```

```sh
source .env && docker exec $(docker-compose ps -q mysqldb) mysqldump --all-databases -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" > "data/db/dumps/db.sql"
```

#### Restoring a backup of all databases

```sh
source .env && docker exec -i $(docker-compose ps -q mysqldb) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/db.sql"
```

#### Creating a backup of single database

**`Notice:`** Replace "YOUR_DB_NAME" by your custom name.

```sh
source .env && docker exec $(docker-compose ps -q mysqldb) mysqldump -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" --databases YOUR_DB_NAME > "data/db/dumps/YOUR_DB_NAME_dump.sql"
```

#### Restoring a backup of single database

```sh
source .env && docker exec -i $(docker-compose ps -q mysqldb) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/YOUR_DB_NAME_dump.sql"
```


#### Connecting MySQL from [PDO](http://php.net/manual/en/book.pdo.php)

```php
<?php
    try {
        $dsn = 'mysql:host=mysql;dbname=test;charset=utf8;port=3306';
        $pdo = new PDO($dsn, 'dev', 'dev');
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>
```

___

## Help us

Any thought, feedback or (hopefully not!)