# eZ Publish 5 Migrations

[![Latest Stable Version](https://poser.pugx.org/kreait/ezpublish-migrations-bundle/v/stable.png)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![Latest Unstable Version](https://poser.pugx.org/kreait/ezpublish-migrations-bundle/v/unstable.png)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)
[![License](https://poser.pugx.org/kreait/ezpublish-migrations-bundle/license.png)](https://packagist.org/packages/kreait/ezpublish-migrations-bundle)

Migrations for eZ Publish 5, almost identical to Symfony's
[DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle).



## Installation

Follow these steps to install the bundle in your eZ Publish 5 project.

Add the following to your composer.json file:

```
{
    "require": {
        "doctrine/migrations": "dev-master",
        "kreait/ezpublish-migrations-bundle": "~1"
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
[official documention](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html)
for further information.

## Examples

### Enable users with the 'Anonymous' role to access the siteacess 'mysiteaccess' without having to log in

```bash
$ ezpublish/console ezpublish:migrations:generate
Generated new migration class to "/var/www/ezpublish/EzPublishMigrations/Version20140508021924.php"
```

```php
// ezpublish/EzPublishMigrations/Version20140508021924.php
namespace Application\Migrations;

use ...

class Version20140508021924 extends AbstractEzPublishMigration
{
    /**
     * @var string
     */
    private $siteAccessIdentifier = 'mysiteaccess';

    /**
     * Removes all existing siteaccess limitations and adds a new one for the role 'Anonymous'
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /** @var $repository \eZ\Publish\API\Repository\Repository */
        $repository = $this->getContainer()->get('ezpublish.api.repository');
        $userService = $repository->getUserService();
        $administratorUser = $userService->loadUser( 14 );
        $repository->setCurrentUser( $administratorUser );

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRole(1); // Anonymous

        $limitation = new SiteAccessLimitation();
        $limitation->limitationValues[] = sprintf('%u', crc32($this->siteAccessIdentifier));

        $policy = $roleService->newPolicyCreateStruct('user', 'login');
        $policy->addLimitation($limitation);

        $roleService->addPolicy($role, $policy);

        $message = sprintf("SELECT 'Added SiteAccess limitation for \"%s\" to role \"%s\"'", $this->siteAccessIdentifier, $role->identifier);
        $this->addSql($message);
    }

    /**
     * Removes access to the new siteaccess for the role 'Anonymous'
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("SELECT 'We should probably remove access to {$this->siteAccessIdentifier} here'");
    }
}

```

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

- [Doctrine Project](http://www.doctrine-project.org/) for providing the underlying migration functionality
- [Symfony](http://symfony.com/) for being the blueprint for this bundle
- [Magic Internet GmbH](http://www.magicinternet.de/), especially [@m-keil](https://github.com/m-keil) for the initial methodical blueprint
