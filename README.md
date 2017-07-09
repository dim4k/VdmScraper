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
