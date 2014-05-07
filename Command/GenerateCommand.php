<?php
/**
 * This file is part of the kreait eZ Publish Migrations Bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Kreait\EzPublish\MigrationsBundle\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Kreait\EzPublish\MigrationsBundle\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand as BaseGenerateCommand;
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

use Kreait\EzPublish\MigrationsBundle\Migrations\AbstractMigration as AbstractEzPublishMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version<version> extends AbstractEzPublishMigration
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
        BaseGenerateCommand::configure();

        $this->setName('ezpublish:migrations:generate');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getApplication()->getKernel()->getContainer();

        $this->setMigrationConfiguration($this->getBasicConfiguration($container, $output));

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->configureMigrations($container, $configuration);

        BaseGenerateCommand::execute($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateMigration(Configuration $configuration, InputInterface $input, $version, $up = null, $down = null)
    {
        $placeHolders = array(
            '<namespace>',
            '<version>',
            '<up>',
            '<down>'
        );
        $replacements = array(
            $configuration->getMigrationsNamespace(),
            $version,
            $up ? "        " . implode("\n        ", explode("\n", $up)) : null,
            $down ? "        " . implode("\n        ", explode("\n", $down)) : null
        );
        $code = str_replace($placeHolders, $replacements, $this->template);
        $dir = $configuration->getMigrationsDirectory();
        $dir = $dir ? $dir : getcwd();
        $dir = rtrim($dir, '/');
        $path = $dir . '/Version' . $version . '.php';

        if ( ! file_exists($dir)) {
            throw new \InvalidArgumentException(sprintf('Migrations directory "%s" does not exist.', $dir));
        }

        file_put_contents($path, $code);

        if ($editorCmd = $input->getOption('editor-cmd')) {
            shell_exec($editorCmd . ' ' . escapeshellarg($path));
        }

        return $path;
    }
}
