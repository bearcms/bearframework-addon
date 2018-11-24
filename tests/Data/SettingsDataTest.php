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
class SettingsDataTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testSettings()
    {
        $app = $this->getApp();

        $settings = Internal2::$data2->settings->get();
        $this->assertTrue($settings['title'] === 'MY COMPANY'); // This is the default value

        $app->data->setValue('bearcms/settings.json', '{
    "title": "MY COMPANY 2",
    "description": "The slogan of my company 2",
    "language": "en",
    "allowSearchEngines": true,
    "externalLinks": true,
    "keywords": ""
}');

        $settings = Internal2::$data2->settings->get();
        $this->assertTrue($settings['title'] === 'MY COMPANY 2');
    }

}
