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
class BlogPostsElementTest extends BearFrameworkAddonTestCase
{

    private function requireEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') !== false);
    }

    private function requireNotEditable($result)
    {
        $this->assertTrue(strpos($result, '<div id="brelc') === false);
    }

    /**
     * 
     */
    public function testEditable()
    {
        $app = $this->getApp();
        $this->createAndLoginUser();

        $result = $app->components->process('<component src="bearcms-blog-posts-element" id="sample-element-1" editable="true"/>');
        $this->requireEditable($result);
    }

    /**
     * 
     */
    public function testNotEditable()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-blog-posts-element"/>');
        $this->requireNotEditable($result);

        $result = $app->components->process('<component src="bearcms-blog-posts-element" id="sample-element-1" editable="true"/>');
        $this->requireNotEditable($result);
    }

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();

        $result = $app->components->process('<component src="bearcms-blog-posts-element"/>');
        //todo
        //$this->assertTrue(strpos($result, '') !== false);
    }

}
