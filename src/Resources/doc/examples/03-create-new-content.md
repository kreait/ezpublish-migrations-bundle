# Example 03

##### Create new content

```bash
$ ezpublish/console ezpublish:migrations:generate
Generated new migration class to "/var/www/ezpublish/EzPublishMigrations/Version20150610145137.php"
```

```php

// ezpublish/EzPublishMigrations/Version20150610145137.php
<?php
namespace Application\Migrations;

use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as EzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150610145137 extends EzPublishMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createContent(2, 'folder', 'eng-GB', [
            'name' => 'This is a new folder',
            'short_name' => 'New folder'
        ]);

        $this->addSql(sprintf("SELECT \"Created new Folder '%s'\"", 'New folder'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}

```