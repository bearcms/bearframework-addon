<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class SettingsDataTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testSettings()
    {
        $app = $this->getApp();

        $settingsDir = $app->config->dataDir . '/objects/bearcms/';
        mkdir($settingsDir, 0777, true);

        $settings = $app->bearCMS->data->settings->get();
        $this->assertTrue($settings['title'] === '');

        file_put_contents($settingsDir . 'settings.json', '{
    "title": "MY COMPANY",
    "description": "The slogan of my company",
    "language": "en",
    "allowSearchEngines": true,
    "externalLinks": true,
    "keywords": ""
}');

        $settings = $app->bearCMS->data->settings->get();
        $this->assertTrue($settings['title'] === 'MY COMPANY');
    }

}
