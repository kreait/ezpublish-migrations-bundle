<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsGenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\MigrationDirectoryHelper;

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

    protected function generateMigration(Configuration $configuration, InputInterface $input, $version, $up = null, $down = null)
    {
        $placeHolders = [
            '<namespace>',
            '<version>',
            '<up>',
            '<down>',
        ];
        $replacements = [
            $configuration->getMigrationsNamespace(),
            $version,
            $up ? "        " . implode("\n        ", explode("\n", $up)) : null,
            $down ? "        " . implode("\n        ", explode("\n", $down)) : null
        ];
        $code = str_replace($placeHolders, $replacements, $this->ezMigrationTemplate);
        $code = preg_replace('/^ +$/m', '', $code);
        $migrationDirectoryHelper = new MigrationDirectoryHelper($configuration);
        $dir = $migrationDirectoryHelper->getMigrationDirectory();
        $path = $dir . '/Version' . $version . '.php';

        file_put_contents($path, $code);

        if ($editorCmd = $input->getOption('editor-cmd')) {
            proc_open($editorCmd . ' ' . escapeshellarg($path), [], $pipes);
        }

        return $path;
    }
}
