<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class DataTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testSettings()
    {
        $app = $this->getApp();
        $settings = $app->bearCMS->data->settings->get();
        $this->assertTrue($settings['title'] === '');
    }

}
