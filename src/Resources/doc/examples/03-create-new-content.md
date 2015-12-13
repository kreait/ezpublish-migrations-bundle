# Example 03

##### Create new content

```php
namespace Application\Migrations;

use Kreait\EzPublish\MigrationsBundle\Migrations\EzPublishMigration;
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
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // We probably should somehow delete the content again.
    }
}

```