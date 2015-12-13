# eZ Publish/Platform Migrations Upgrade

## From 2.0 to 3.0

### Project dependencies

This bundle now requires the [`DoctrineMigrationsBundle`](https://github.com/doctrine/DoctrineMigrationsBundle):

```php
// ezpublish/EzPublishKernel.php (eZ Publish 5)
// or
// app/AppKernel.php (eZ Platform)
public function registerBundles()
{
    $bundles = array(
        //...
        new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        new Kreait\EzPublish\MigrationsBundle\KreaitEzPublishMigrationsBundle(),
    );
}
```

### Configuration changes

The configuration has changed as described in the following table:

2.0                                 | 3.0
----------------------------------- | -------------------------------------------
**ezpublish_migrations**.dir_name   | **doctrine_migrations**.dir_name
**ezpublish_migrations**.namespace  | **doctrine_migrations**.namespace
**ezpublish_migrations**.table_name | **doctrine_migrations**.table_name
**ezpublish_migrations**.name       | **doctrine_migrations**.name
ezpublish_migrations.**ez_user**    | ezpublish_migrations.**ez_migrations_user**

So, the following 2.0 configuration

```yaml
# ezpublish/config/config.yml (eZ Publish 5)
# app/config/config.yml (eZ Platform)
ezpublish_migrations:
    dir_name: "%kernel.root_dir%/EzPublishMigrations"
    namespace: Application\Migrations
    table_name: ezmigration_versions
    name: Application Migrations
    ez_user: admin
```

would become

```yaml
# ezpublish/config/config.yml (eZ Publish 5)
# app/config/config.yml (eZ Platform)
doctrine_migrations:
    dir_name: "%kernel.root_dir%/EzPublishMigrations"
    namespace: Application\Migrations
    table_name: ezmigration_versions
    name: Application Migrations

ezpublish_migrations:
    ez_migrations_user: admin
```

### Console command changes

The `ezpublish:migrations:generate` commands remains unchanged.

2.0                              | 3.0
---------------------------------| -------------------------------
**ezpublish**:migrations:execute | **doctrine**:migrations:execute
**ezpublish**:migrations:latest  | **doctrine**:migrations:latest
**ezpublish**:migrations:migrate | **doctrine**:migrations:migrate
**ezpublish**:migrations:status  | **doctrine**:migrations:status
**ezpublish**:migrations:version | **doctrine**:migrations:version
