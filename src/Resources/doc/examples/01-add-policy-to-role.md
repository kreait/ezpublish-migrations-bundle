# Example 01

##### Enable users with the 'Anonymous' role to access the siteacess 'mysiteaccess' without having to log in

```php
namespace Application\Migrations;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Limitation\SiteAccessLimitation;
use eZ\Publish\API\Repository\Values\User\Role;
use Kreait\EzPublish\MigrationsBundle\Migrations\EzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Enables the anonymous user to access a siteaccess without having to log in
 */
class Version20140507140029 extends EzPublishMigration
{
    /**
     * @var string
     */
    private $siteAccessIdentifier = 'site';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $roleService = $this->repository->getRoleService();

        $role = $roleService->loadRole(1); // Anonymous

        $this->addSiteAccessLimitation($roleService, $role, $this->siteAccessIdentifier);
    }

    /**
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // We should probably remove access to {$this->siteAccessIdentifier} here
    }

    /**
     * Adds a user/login policy to the given role for the given siteaccess name
     *
     * @param RoleService $roleService
     * @param Role $role
     * @param string $siteAccessName
     */
    protected function addSiteAccessLimitation(RoleService $roleService, Role $role, $siteAccessName)
    {
        $limitation = new SiteAccessLimitation();
        $limitation->limitationValues[] = sprintf('%u', crc32($siteAccessName));

        $policy = $roleService->newPolicyCreateStruct('user', 'login');
        $policy->addLimitation($limitation);

        $roleService->addPolicy($role, $policy);
    }
}
```