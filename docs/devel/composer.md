Composer
========

Introduction
------------

[Composer](https://getcomposer.org/) is a PHP package management tool that has become the de facto standard for the PHP community. 

This page gives a basic explanation of Composer and focuses on the usage of Composer in the La Danse Guild Website project. 
For more complete documentation please visit the [Composer documentation pages](https://getcomposer.org/doc/).

On the website [Packagist](https://packagist.org/) you can search for or browse all packages and their available versions and dependencies.

Installing Composer
-------------------

You can install `composer` locally or globally. We suggest to install it globally as it will be used by most of your PHP projects.

Instructions can be found [here](https://getcomposer.org/doc/00-intro.md). 

This documentation assumes there is a binary called `composer` on your PATH.

Updating Composer
-----------------

Composer has a self-update mechanism that can easily be triggered by running the following command:

~~~~
composer self-update
~~~~

This will verify with GitHub if there is a new version available and if yes downloads this. The user running this command needs write access to the `composer` file on your PATH.

Configuring Composer
--------------------

To configure composer you create a file "composer.json" where you add the external packages you will be using in your project.

The `composer.json` for the Symfony Demo application can be found below.

~~~~
{
    "name": "symfony/symfony-demo",
    "license": "MIT",
    "type": "project",
    "description": "Symfony Demo Application",
    "autoload": {
        "psr-4": { "": "src/" },
        "classmap": [ "app/AppKernel.php", "app/AppCache.php" ]
    },
    "autoload-dev": {
        "psr-4": { "Tests\\": "tests/" }
    },
    "require": {
        "php"                                  : ">=5.5.9",
        "ext-pdo_sqlite"                       : "*",
        "doctrine/doctrine-bundle"             : "^1.6",
        "doctrine/doctrine-fixtures-bundle"    : "^2.2",
        "doctrine/orm"                         : "^2.5",
        "erusev/parsedown"                     : "^1.5",
        "ezyang/htmlpurifier"                  : "^4.7",
        "incenteev/composer-parameter-handler" : "^2.0",
        "leafo/scssphp"                        : "^0.5",
        "patchwork/jsqueeze"                   : "^2.0",
        "sensio/distribution-bundle"           : "^5.0",
        "sensio/framework-extra-bundle"        : "^3.0",
        "symfony/assetic-bundle"               : "^2.8",
        "symfony/monolog-bundle"               : "^2.8",
        "symfony/swiftmailer-bundle"           : "^2.3",
        "symfony/symfony"                      : "^3.1",
        "twig/extensions"                      : "^1.3",
        "white-october/pagerfanta-bundle"      : "^1.0"
    },
    "require-dev": {
        "sensio/generator-bundle"              : "~3.0",
        "symfony/phpunit-bridge"               : "^3.0"
    },
    "scripts": {
        "post-install-cmd": [
            "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
        ],
        "post-update-cmd": [
            "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
        ]
    },
    "config": {
        "bin-dir": "bin",
        "platform": {
            "php": "5.5.9"
        }
    },
    "extra": {
        "symfony-app-dir": "app",
        "symfony-bin-dir": "bin",
        "symfony-var-dir": "var",
        "symfony-web-dir": "web",
        "symfony-tests-dir": "tests",
        "symfony-assets-install": "relative",
        "incenteev-parameters": {
            "file": "app/config/parameters.yml",
            "env-map": {
                "database_url": "DATABASE_URL",
                "secret": "SYMFONY_SECRET"
            }
        }
    }
}
~~~~

Note the `scripts` section in this file. It will instruct Composer to run code from the installed packages after the update or install has finished.

The La Danse Guild Website already includes a `composer.json` file in the Git repository.

Package Versioning
------------------

Composer has a flexible mechanism to indicate which version of a package you want. More information can be found [here](https://getcomposer.org/doc/articles/versions.md) but we give a short overview below.

- Exact `1.0.2`: use the exact version stated and nothing else
- Range `>=1.0 <1.1 || >=1.2`: use the highest version that satisfies the range and dependencies
- Range (Hyphen) `1.0 - 2.0`: use the highest version that satisfies the range and dependencies
- Wildcard `1.0.*`: use the highest version that satisfies the wildcard expression and dependencies
- Tilde `~1.2`: this is equivalant to `>=1.2 <2.0.0`
- Caret `^1.2.3`: this is equivalant to `>=1.2.3 <2.0.0`

The difference between the Tilde and Caret versioning is subtle and deals with semantic versioning. See the documentation for more information.

Using Composer
--------------

There are two commands you will use to manage and install your third party dependencies.

~~~~
composer update
~~~~

The update command will check for every third party package if there is a newer version available that fullfils the versioning constraints and dependencies. This might take several minutes as `composer` has to fetch versioning information for each package and its dependencies from GitHub.

After this command has finished, Composer will write the actual installed version for each package in a file named `composer.lock`. This file is entirely managed by Composer and should never be manipulated manually.

The second command you will use if install:

~~~~
composer install
~~~~

The install command will only use the information available in `composer.lock` and download and install the versions found in there.

Both commands will run the scripts indicated in the `composer.json` file. That will typically result in side effects in other areas of the project.

Running `composer install` versus `composer update`
--------------------------------------------------

As we learned above, there is a a differene between running the install or the update the command. The install command will merely use the information found in `composer.lock` to install the right versions of third party packages. The update command on the other hand will reach out of GitHub and see if new versions are available.

It is considered best practice to store `composer.lock` in your git repository as well. Doing so will guarantee that the production environment will use the same versions of third party packages as the development environment. This of course assumes you only run the install command in the production environment.

If you don't store `composer.lock` in your git repository, you are forced to run the install command in the production environment which may result in newer versions of third party packages being used in production.

When version constraints in `composer.json` are updated or new packages added, developer should synchronize among each other so all of them can update their local development environment.

Memory Requirements
-------------------

Composer is a very memory-hungry application. On most PHP installations the standard maxmimum amount of memory that can be allocated to a PHP script is often not enough.

To increase the maximum amount of memory that can be allocated to a PHP script we need to update a property in the `php.ini` file. To find the location of this file for your PHP installation you can run the following command in a terminal:

~~~~
php -i | grep "Loaded Configuration File"
~~~~

The output of this command gives you the location of the `php.ini` file. Open this file with your favorite text editor. Search for the property `memory_limit`. You can set this to a much higher value (e.g. 512MB or higher). Another option is to set this to `-1` which indicates there is no limit.