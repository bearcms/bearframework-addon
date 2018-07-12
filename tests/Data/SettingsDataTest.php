<?php

/*
 * BearCMS addon for Bear Framework
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

        $settings = $app->bearCMS->data->settings->get();
        $this->assertTrue($settings['title'] === 'MY COMPANY'); // This is the default value

        $app->data->setValue('bearcms/settings.json', '{
    "title": "MY COMPANY 2",
    "description": "The slogan of my company 2",
    "language": "en",
    "allowSearchEngines": true,
    "externalLinks": true,
    "keywords": ""
}');

        $settings = $app->bearCMS->data->settings->get();
        $this->assertTrue($settings['title'] === 'MY COMPANY 2');
    }

}
