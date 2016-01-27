<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsGenerateDoctrineCommand;

/**
 * Command for generating new blank migration classes.
 */
class GenerateCommand extends MigrationsGenerateDoctrineCommand
{
    protected $ezMigrationTemplate =
            '<?php
namespace <namespace>;

use Kreait\EzPublish\MigrationsBundle\Migrations\EzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version<version> extends EzPublishMigration
{
    /**
     * Description of this up migration
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
<up>
    }

    /**
     * Description of this down migration
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
<down>
    }
}
';

    protected function configure()
    {
        parent::configure();

        $this->setName('ezpublish:migrations:generate');
        $this->setDescription('Generate a blank eZ Publish/Platform enabled migration class');
    }

    protected function getTemplate()
    {
        return $this->ezMigrationTemplate;
    }
}
