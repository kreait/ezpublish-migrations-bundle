<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle;

use Kreait\EzPublish\MigrationsBundle\Helper\HelperSet;

interface Helper
{
    /**
     * @return string
     */
    public function getName();

    public function setHelperSet(HelperSet $helperSet);
}
