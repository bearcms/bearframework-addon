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
class LinkElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-link-element" url="https://bearcms.com/" text="BearCMS" title="BearCMS"/>');
        $this->assertTrue(strpos($result, 'href="https://bearcms.com/"') !== false);
        $this->assertTrue(strpos($result, 'title="BearCMS') !== false);
        $this->assertTrue(strpos($result, '>BearCMS<') !== false);
        
        $result = $app->components->process('<bearcms-link-element url="https://bearcms.com/" text="BearCMS" title="BearCMS"/>');
        $this->assertTrue(strpos($result, 'href="https://bearcms.com/"') !== false);
        $this->assertTrue(strpos($result, 'title="BearCMS') !== false);
        $this->assertTrue(strpos($result, '>BearCMS<') !== false);
    }

}
