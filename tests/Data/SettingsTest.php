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
class SettingsTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testData()
    {
        $app = $this->getApp();
        $app->data->setValue('bearcms/settings.json', json_encode([
            'title' => 'My website!'
        ]));
        $settings = $app->bearCMS->data->settings->get();
        $this->assertEquals($settings->title, 'My website!');
        $this->assertEquals($settings->description, null);
        $this->assertEquals($settings->keywords, null);
        $this->assertEquals($settings->language, 'en');
        $this->assertEquals($settings->icon, null);
        $this->assertEquals($settings->externalLinks, false);
        $this->assertEquals($settings->allowSearchEngines, false);
        $this->assertEquals($settings->allowCommentsInBlogPosts, false);
        $this->assertEquals($settings->disabled, false);
        $this->assertEquals($settings->disabledText, null);
        $this->assertEquals($settings->enableRSS, true);
        $this->assertEquals($settings->rssType, 'contentSummary');
    }

}
