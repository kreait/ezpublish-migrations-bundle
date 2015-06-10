<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand as BaseGenerateCommand;
use Kreait\EzPublish\MigrationsBundle\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command for generating new blank migration classes.
 */
class GenerateCommand extends BaseGenerateCommand
{
    use CommandTrait;

    protected $template =
            '<?php
namespace <namespace>;

use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as EzPublishMigration;
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
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('ezpublish:migrations:generate');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Bundle\FrameworkBundle\Console\Application $app */
        $app = $this->getApplication();
        /** @var ContainerInterface $container */
        $container = $app->getKernel()->getContainer();

        $this->setMigrationConfiguration($this->getBasicConfiguration($container, $output));

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->configureMigrations($container, $configuration);

        parent::execute($input, $output);
    }

    /**
     * {@inheritDoc}
     */
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
            $up ? '        '.implode("\n        ", explode("\n", $up)) : null,
            $down ? '        '.implode("\n        ", explode("\n", $down)) : null,
        ];
        $code = str_replace($placeHolders, $replacements, $this->template);
        $dir = $configuration->getMigrationsDirectory();
        $dir = $dir ? $dir : getcwd();
        $dir = rtrim($dir, '/');
        $path = $dir.'/Version'.$version.'.php';

        if (!file_exists($dir)) {
            // @codeCoverageIgnoreStart
            throw new \InvalidArgumentException(sprintf('Migrations directory "%s" does not exist.', $dir));
            // @codeCoverageIgnoreEnd
        }

        file_put_contents($path, $code);

        if ($editorCmd = $input->getOption('editor-cmd')) {
            shell_exec($editorCmd.' '.escapeshellarg($path));
        }

        return $path;
    }
}
