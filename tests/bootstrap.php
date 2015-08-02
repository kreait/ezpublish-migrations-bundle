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
$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Doctrine\DBAL\Migrations\Tests', __DIR__.'/../vendor/doctrine/migrations/tests');
$loader->addPsr4('Kreait\\EzPublish\\MigrationsBundle\\Tests\\', __DIR__.'/Tests');
