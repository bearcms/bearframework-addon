<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class AddonsTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testAnnounce()
    {
        $app = $this->getApp();
        $app->bearCMS->addons->announce('verdor1/addon1', function(\BearCMS\Addons\Addon $addon) {
            
        });
    }

}
