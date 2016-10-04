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
class LinkElementTest extends BearFrameworkAddonTestCase
{

    private function requireEditable($result)
    {
        $this->assertTrue(strpos($result, '<body><div id="brelc') !== false);
    }

    private function requireNotEditable($result)
    {
        $this->assertTrue(strpos($result, '<body><div id="brelc') === false);
    }

    /**
     * 
     */
    public function testEditable()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-link-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-link-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-link-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-link-element" url="https://bearcms.com/" text="Bear CMS" title="Bear CMS"/>');
        $this->assertTrue(strpos($result, 'href="https://bearcms.com/"') !== false);
        $this->assertTrue(strpos($result, 'title="Bear CMS') !== false);
        $this->assertTrue(strpos($result, '>Bear CMS<') !== false);
    }

}