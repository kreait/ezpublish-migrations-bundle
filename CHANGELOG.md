# CHANGELOG

## Unreleased

- Pin doctrine/migrations - the latest master did not work anymore.

## 1.1.1 - 2015-03-31

### Bugfix

The field `$repository` in the `AbstractMigration` had been change from `protected` to `private`, although it
should be accessible to use from generated migrations. Now it's back to `protected` again :).

Thanks to @nadiri for bringing this up.

## 1.1 - 2015-03-25

### What's new

* The default migration user is now configurable by setting the `ez_user` parameter. If not set, it falls back to 
  `admin` (User ID 14), which is the standard admin user in eZ Publish
* It is now possible to change the migration user during a migration by using the following methods:

```php
public function up(Schema $schema)
{
    $this->changeMigrationUser('another_ez_username');
    // ...
    $this->restoreDefaultMigrationUser();
}
```

## 1.0.3 - 2014-12-17

### Changes
- We now don't add the complete eZ Publish kernel as a dev dependency, but only the API/SPI libraries
- Use retina ready badges for the README

### Bugfixes
- Fixed the VersionCommand unit test, which wasn't configured with the `--no-interaction` flag


## 1.0.2 - 2014-05-22

### What's new
- PHPUnit Tests with full code coverage
- As this is a bundle for eZ Publish 5, it now follows the eZ Publish Coding Standards
- Integrated Travis-CI and Scrutinizer-CI
- Added .gitattributes file for smaller release packages

### Changes
- The class alias in generated migrations has been changed from `AbstractEzPublishMigration` to `EzPublishMigration` -
  you don't need to change your already existing migrations.

## 1.0.1 - 2014-05-08

### What's new
- Shortcut for the eZ Publish API Repository, you can now directly access it through `$this->repository`
- The default admin user is set as current by default
- Fixed typos
- Fixed version numbers in `composer.json`
- Moved documentation to `Resources/doc/examples`
- Added `ezsystems/ezpublish-kernel` as a dev requirement

### Deprecated
- The method `$this->getContainer()` is deprecated, use `$this->container` instead

## 1.0 - 2014-05-08

Initial release.