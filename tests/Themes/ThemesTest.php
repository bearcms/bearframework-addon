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
class ThemesTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testRegister()
    {
        $app = $this->getApp();
        $app->bearCMS->themes->register('verdor1/theme1', function(\BearCMS\Themes\Theme $theme) {
            
        });
    }

}
