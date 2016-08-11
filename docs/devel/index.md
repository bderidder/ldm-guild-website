Developer Guide
===============

Introduction
------------

This guide is meant to help contributors setup a local development environment. This guide assumes a Unix-like system like Linux or OS X.

Prerequisites
-------------

We assume the following software is already installed and functioning:

- nodejs (4.4 or 6.3)
- less compiler 
- bower
- PHP 7.0 (current development is done on 7.0)
- MySQL (current development is done on 5.7)

Installation Steps
------------------

### Create Database

You will need a database defined in MySQL and a user that has read and write access to that database including the rights to create, drop or alter tables. You can use the following commands in MySQL.

~~~~
> CREATE DATABASE ladansedevdb;
> GRANT ALL ON ladansedevdb.* TO 'ladanse'@'localhost' IDENTIFIED BY 'password';
~~~~

Note that the user must have sufficient access rights to create, alter and drop tables in the database.

You can replace the name of the database, the user or the password to better suit your local environment or preferences. Write down the name of the database, the username and the password. It will be required later during the installation process.

### Clone GitHub Repository

You can clone the Git repository using the command below. 

~~~~
git clone https://github.com/bderidder/ldm-guild-website.git
~~~~

This will create a folder called `ldm-guild-website` in which the project files can be found.

### Create parameters.yml

At this moment we have the project files installed in a folder `ldm-guild-website`. Normally we should start installing all the PHP pacakges we depend on using `composer`. As part of this installation step, composer will also create a local configuration based on the template configuration found in `app/config/parameters.yml.dist`. It will ask a series of questions to gather values for this configuration. To avoid this rather unfriendly step we provide a `parameters.yml` file before we run composer.
 
First we copy `app/config/parameters.yml.dist` to `app/config/parameters.yml`. Open this newly created file with your favorite text editor and edit the values of the properties you find in there. 

At least the following properties need to be updated to be able to connect to the database:

- database_host
- database_port
- database_name
- database_user
- database_password

The following properties must point to the local installation of some Unix tools:

- nodejs_binary_path
- bower_binary_path

You can find these paths by running the following commands:

~~~~
type node
type bower
~~~~

All the other properties are not required immediately but can be updated as well.

### Install Composer Packages

You need to have `composer` installed. Instructions on how to do this and some additional explanations on the use of `composer` can be found [here](composer.md).

Since we have a `composer.lock` stored in the Git repository we can simply run the following command to download and install all third party packages we depend on:

~~~~
composer install
~~~~

This may take a few minutes depending on the speed of your Internet and the performance of your computer.

At this moment we have almost a fully functioning application: all third party libraries and packages are installed and we have a local configuration file.

### Populate Database

The database schema and master data (like the list of races and game classes) is managed by [Doctrine Migrations](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html). The migrations steps themselves are stored in PHP files in `app/DoctrineMigrations`. 
 
We will now instruct Doctrine Migrations to read these files and execute them, in the correct chronological order as indicated by the filenames, on the configured database.

~~~~
php bin/console doctrine:migrations:migrate --env=dev --allow-no-migration --no-interaction
~~~~

The options given have the following meaning

`--env=dev` instructs Symfony to run the command using the development environment settings
`--allow-no-migration` do not complain if the database is already up-to-date and no migrations have to be executed
`--no-interaction` do not ask for confirmation, just execute the migrations when needed

Doctrine Migrations adds the table `migration_versions` to the database to keep track of which migrations have been installed and which haven't.

### Install Assets

To be written.

A more in-depth explanation of how composer works and why we are running this particular command can be found [here](assetic.md).

- Install web assets
- Dump assets

### Install Bower Packages

To be written.

Additional Setup
----------------

### Configure PHPStorm

To be written.