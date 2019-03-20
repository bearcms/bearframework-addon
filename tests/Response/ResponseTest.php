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
class ResponseTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testApply()
    {
        $app = $this->getApp();
        $response = new \BearFramework\App\Response\HTML('Hi');
        $app->bearCMS->apply($response);
        $this->assertTrue(strpos($response->content, '<meta name="generator" content="Bear CMS (powered by Bear Framework)">') !== false);
    }

}
