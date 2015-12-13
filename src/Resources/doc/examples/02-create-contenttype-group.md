# Example 02

##### Create a ContentType group

```php
namespace Application\Migrations;

use Kreait\EzPublish\MigrationsBundle\Migrations\EzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates a new content type group
 */
class Version20140508144959 extends EzPublishMigration
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
        $contentTypeService = $this->repository->getContentTypeService();

        // Check if the group already exists
        $existingGroups = $contentTypeService->loadContentTypeGroups();
        foreach ($existingGroups as $g) {
            $this->skipIf($g->identifier == $this->groupName, "The group '{$this->groupName}' already exists");
        }

        // Create the group
        $contentTypeService->createContentTypeGroup(
            $contentTypeService->newContentTypeGroupCreateStruct($this->groupName)
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // We don't delete existing ContentType groups so that we don't break things
    }
}
```