# Example 02

##### Create a ContentType group

```bash
$ ezpublish/console ezpublish:migrations:generate
Generated new migration class to "/var/www/ezpublish/EzPublishMigrations/Version20140508144959.php"
```

```php
// ezpublish/EzPublishMigrations/Version20140508144959.php
<?php

namespace Application\Migrations;

use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as AbstractEzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates a new content type group
 */
class Version20140508144959 extends AbstractEzPublishMigration
{
    /**
     * @var string
     */
    protected $groupName = 'My Custom Groupname';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $userService = $this->repository->getUserService();
        $contentTypeService = $this->repository->getContentTypeService();

        // Set current user to admin
        $administratorUser = $userService->loadUser( 14 );
        $this->repository->setCurrentUser( $administratorUser );

        // Check if the group already exists
        $existingGroups = $contentTypeService->loadContentTypeGroups();
        foreach ($existingGroups as $g) {
            $this->skipIf($g->identifier == $this->groupName, "The group '{$this->groupName}' already exists");
        }

        // Create the group
        $contentTypeService->createContentTypeGroup(
            $contentTypeService->newContentTypeGroupCreateStruct($this->groupName)
        );

        $this->addSql(sprintf("SELECT \"Created new ContentType group '%s'\"", $this->groupName));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("SELECT \"We don't delete existing ContentType groups so that we don't break things.\"");
    }
}
```