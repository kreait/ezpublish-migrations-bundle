<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Helper;

trait HelperTrait
{
    /**
     * @var HelperSet
     */
    protected $helperSet;

    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;
    }
}
