# eZ Publish 5 Migrations

[![Latest Stable Version](https://img.shields.io/packagist/v/kreait/ezpublish-migrations-bundle.svg)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![License](http://img.shields.io/badge/Licence-MIT-blue.svg)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![Build Status](https://img.shields.io/travis/kreait/ezpublish-migrations-bundle.svg)](http://travis-ci.org/kreait/ezpublish-migrations-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/kreait/ezpublish-migrations-bundle.svg)](https://scrutinizer-ci.com/g/kreait/ezpublish-migrations-bundle/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/kreait/ezpublish-migrations-bundle.svg)](https://scrutinizer-ci.com/g/kreait/ezpublish-migrations-bundle/?branch=master)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-ff69b4.svg)](https://gitter.im/kreait/ezpublish-migrations-bundle)

Migrations for eZ Publish 5, based on [Doctrine Migrations](https://github.com/doctrine/migrations), very similar to Symfony's
[DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle).



## Installation

```bash
composer require kreait/ezpublish-migrations-bundle
```

## Configuration


Enable the bundle in EzPublishKernel.php by including the following:

```php
// ezpublish/EzPublishKernel.php
public function registerBundles()
{
    $bundles = array(
        //...
        new Kreait\EzPublish\MigrationsBundle\KreaitEzPublishMigrationsBundle(),
    );
}
```

You can configure the path, namespace, table_name and name in your config.yml.
The examples below are the default values.

```
// ezpublish/config/config.yml
ezpublish_migrations:
    dir_name: "%kernel.root_dir%/EzPublishMigrations"
    namespace: Application\Migrations
    table_name: ezmigration_versions
    name: Application Migrations
    ez_user: admin
```

## Usage

All of the migrations functionality is contained in the following commands:

```
ezpublish:migrations
  :execute  Execute a single migration version up or down manually.
  :generate Generate a blank migration class.
  :migrate  Execute a migration to a specified version or the latest available version.
  :status   View the status of a set of migrations.
  :version  Manually add and delete migration versions from the version table.
```

The usage is identical to Symfony's DoctrineMigrationBundle, except for the missing `:diff` command.
Please have a look at the
[official documentation](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html)
for further information.

### Changing the current migration user during a migration

You can change the current eZ Publish user inside a migration by issuing the following command:

```php
$this->changeMigrationUser('another_username');
```

and restore the default Migration user by using:

```php
$this->restoreDefaultMigrationUser();
```

## Usage examples

See [src/Resources/doc/examples](src/Resources/doc/examples) for some usage examples.

## Caveats

### No "real" SQL statements

When you create a migration using only eZ Publish's API methods, no actual SQL statements are executed. This results in the following message:

```
Migration was executed but did not result in any SQL statements.
```

You can avoid this message by adding a dummy SQL statement at the end of your `up()` and `down()` method:

```php
public function up(Schema $schema)
{
    // ...
    $this->addSql("SELECT 'Description of what we did here'");
}
```


## Acknowledgments

- [Doctrine Project](http://www.doctrine-project.org/) for the [Doctrine Database Migrations](https://github.com/doctrine/migrations) providing the underlying migration functionality
- [Symfony](http://symfony.com/) for the [DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle) being the blueprint for this bundle
- [Magic Internet GmbH](http://www.magicinternet.de/), especially [@m-keil](https://github.com/m-keil) for the initial methodical blueprint
