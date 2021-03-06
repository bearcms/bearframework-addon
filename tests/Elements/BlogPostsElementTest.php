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
class BlogPostsElementTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testOutput()
    {
        $app = $this->getApp();
        
        $result = $app->components->process('<component src="bearcms-blog-posts-element"/>');
        $this->assertTrue(strpos($result, 'class="bearcms-blog-posts-element"') !== false);
        
        $result = $app->components->process('<bearcms-blog-posts-element/>');
        $this->assertTrue(strpos($result, 'class="bearcms-blog-posts-element"') !== false);
    }

}
