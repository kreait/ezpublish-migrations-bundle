# eZ Publish 5 Migrations

[![Latest Stable Version](https://img.shields.io/packagist/v/kreait/ezpublish-migrations-bundle.svg?style=flat-square)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![Build Status](https://img.shields.io/travis/kreait/ezpublish-migrations-bundle.svg?style=flat-square)](http://travis-ci.org/kreait/ezpublish-migrations-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/kreait/ezpublish-migrations-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/kreait/ezpublish-migrations-bundle/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/kreait/ezpublish-migrations-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/kreait/ezpublish-migrations-bundle/?branch=master)
[![License](http://img.shields.io/badge/Licence-MIT-brightgreen.svg?style=flat-square)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)

Migrations for eZ Publish 5, based on [Doctrine Migrations](https://github.com/doctrine/migrations), very similar to Symfony's
[DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle).



## Installation

Follow these steps to install the bundle in your eZ Publish 5 project.

Add the following to your composer.json file:

```
{
    "require": {
        "doctrine/migrations": "dev-master",
        "kreait/ezpublish-migrations-bundle": "~1.0"
    }
}
```

Update the vendor libraries:

```bash
$ php composer.phar update
```

If everything worked, the EzPublishMigrationsBundle can now be found at vendor/kreait/ezpublish-migrations-bundle.

Finally, be sure to enable the bundle in EzPublishKernel.php by including the following:

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

## Configuration

You can configure the path, namespace, table_name and name in your config.yml.
The examples below are the default values.

```
// ezpublish/config/config.yml
ezpublish_migrations:
    dir_name: %kernel.root_dir%/EzPublishMigrations
    namespace: Application\Migrations
    table_name: ezmigration_versions
    name: Application Migrations
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

## Examples

See [Resources/doc/examples](Resources/doc/examples) for some usage examples.

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
