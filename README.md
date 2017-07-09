VdmScraper
=====

A Web Scraper using Symfony 3

Version
----
Current Version 0.1

Bundle / Libraries used
----

* friendsofsymfony/rest-bundle
* symfony/dom-crawler
* fabpot/goutte

Server requirements
----
* Docker
*Note : You can still deploy this app with a classic PHP5+ / Nginx / MySQL stack, if so be sure to change 'sf3' alias in command by 'php bin/console'and don't mind the 'Enter Docker bash commands' steps.*

Installation
----

#### Clone Github repository

```sh
git clone https://github.com/dim4k/VdmScraper.git
```

#### Run the server

*Build/run Docker containers*
```sh
cd docker-symfony
docker-compose build
docker-compose up -d
```

*Composer install and create database*
```sh
# Enter Docker bash commands
docker-compose exec php bash

# Create Symfony default parameters
cd app/config cp parameters.yml.dist parameters.yml

# Install Composer dependencies
composer install

# Create database
sf3 doctrine:database:create
sf3 doctrine:schema:update --force

# Exit Docker bash commands
exit
```

*Get containers IP address*
```sh
docker network inspect bridge | grep Gateway
```

Execute the scraper
----

```sh
# Enter Docker bash commands
docker-compose exec php bash

# Launch scraping | {limit} : number of post scraped
sf3 app:scrap-vdm {limit}
```

Access the API
----

Use a Rest client or simply use Curl to access the API

* /api/posts
* /api/posts?from=2014-01-01&amp;to=2014-12-31
* /api/posts/&lt;id&gt;

Test the app
----

```sh
# Enter Docker bash commands
docker-compose exec php bash

# Launch tests
php vendor/phpunit/phpunit/phpunit
```

