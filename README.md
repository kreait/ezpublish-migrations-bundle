# eZ Publish/Platform Migrations

[![Latest Stable Version](https://img.shields.io/packagist/v/kreait/ezpublish-migrations-bundle.svg)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/aa58cc1a-da57-4ced-81f1-55bee80b88e3/mini.png)](https://insight.sensiolabs.com/projects/aa58cc1a-da57-4ced-81f1-55bee80b88e3)
[![License](http://img.shields.io/badge/Licence-MIT-blue.svg)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-ff69b4.svg)](https://gitter.im/kreait/ezpublish-migrations-bundle)

Migrations for eZ Publish/Platform.

## Features

This bundle gives you an additional `ezpublish:migrations:generate` command, which generates a Migration that eases
eZ Publish/Platform related changes

- Automatically sets the active eZ user performing the changes (default: `admin`)
- Allows the quick change of the currently active user, e.g. for creating new content in the name of a certain user.
- Adds a shorthand method to create new content

## Installation

```bash
composer require kreait/ezpublish-migrations-bundle
```

Enable the DoctrineMigrationsBundle and the KreaitEzPublishMigrationsBundle
in `AppKernel.php` (eZ Platform) or `EzPublishKernel.php` (eZ Publish 5):

```php
public function registerBundles()
{
    $bundles = array(
        //...
        new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        new Kreait\EzPublish\MigrationsBundle\KreaitEzPublishMigrationsBundle(),
    );
}
```

### Configuration

You can configure the bundles in your `config.yml`. The examples below are the default values.

```
doctrine_migrations:
    dir_name: "%kernel.root_dir%/DoctrineMigrations"
    namespace: Application\Migrations
    table_name: migration_versions
    name: Application Migrations

ezpublish_migrations:
    # The login name of the user performing the migrations.
    ez_migrations_user: admin
```

## Usage

The usage is [identical to Symfony's DoctrineMigrationBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html),
with the addition of the following command:

```bash
# Generate a blank eZ Publish/Platform enabled migration class
$ ezpublish:migrations:generate
```

You can access the eZ Repository inside a migration with `$this->repository`.

See [src/Resources/doc/examples](src/Resources/doc/examples) eZ Publish related example migrations.

### Helper methods

#### Changing the current migration user during a migration

You can change the current eZ Publish user inside a migration by issuing the following command:

```php
// All subsequent calls will be made as the user with the given name
$this->changeMigrationUser('another_username');
```

and restore the default Migration user by using:

```php
// Restores the current user to the configured migrations user.
$this->restoreDefaultMigrationUser();
```

#### Quickly create simple content

```php
$this->createContent($parentLocationId, $contentTypeIdentifier, $languageCode, array $fields);
```

see [Create new content example](src/Resources/doc/examples/03-create-new-content.md)

## Known issues

When you create a migration using only eZ Publish's API methods, no SQL statements are executed in terms of the
DoctrineMigrationsBundle. This results in the following message:

```
Migration was executed but did not result in any SQL statements.
```
