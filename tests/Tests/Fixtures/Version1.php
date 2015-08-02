<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Tests\Fixtures;

use Doctrine\DBAL\Schema\Schema;
use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as EzPublishMigration;

class Version1 extends EzPublishMigration
{
    public function up(Schema $schema)
    {
        $this->createContent(2, 'folder', 'ger-DE', ['title' => 'Title']);
    }

    public function down(Schema $schema)
    {
    }
}
